<?php
/**
 * m4p_homecategories
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class M4p_Homecategories extends Module
{
    private const CONFIG_PARENT = 'M4P_HC_PARENT';
    /** Thumbnails are drawn at 88 px; 160 px keeps them sharp on 2x screens. */
    private const THUMBNAIL_MIN_WIDTH = 160;

    public function __construct()
    {
        $this->name = 'm4p_homecategories';
        $this->tab = 'front_office_features';
        $this->version = '2.0.3';
        $this->author = 'Modules4Presta';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Home catalogue', [], 'Modules.M4phomecategories.Admin');
        $this->description = $this->trans('Shows the catalogue departments with a thumbnail, subcategories and product count on the home page.', [], 'Modules.M4phomecategories.Admin');
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    public function install(): bool
    {
        return parent::install()
            && $this->registerHook(['displayHome', 'actionFrontControllerSetMedia'])
            && Configuration::updateValue(self::CONFIG_PARENT, (int) Configuration::get('PS_HOME_CATEGORY'));
    }

    public function uninstall(): bool
    {
        return Configuration::deleteByName(self::CONFIG_PARENT) && parent::uninstall();
    }

    public function getContent(): string
    {
        $output = '';
        if (Tools::isSubmit('submitM4pHomecategories')) {
            $parentId = (int) Tools::getValue(self::CONFIG_PARENT);
            if (!Validate::isLoadedObject(new Category($parentId))) {
                $output .= $this->displayError($this->trans('Choose a category from the list.', [], 'Modules.M4phomecategories.Admin'));
            } else {
                Configuration::updateValue(self::CONFIG_PARENT, $parentId);
                $output .= $this->displayConfirmation($this->trans('Settings saved.', [], 'Modules.M4phomecategories.Admin'));
            }
        }

        return $output . $this->renderForm();
    }

    private function renderForm(): string
    {
        $options = array_map(
            fn (array $category): array => ['id_category' => (int) $category['id_category'], 'name' => $category['name']],
            Category::getSimpleCategories((int) $this->context->language->id)
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->default_form_language = (int) $this->context->language->id;
        $helper->submit_action = 'submitM4pHomecategories';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars['fields_value'][self::CONFIG_PARENT] = (int) Configuration::get(self::CONFIG_PARENT);

        return $helper->generateForm([[
            'form' => [
                'legend' => ['title' => $this->trans('Settings', [], 'Modules.M4phomecategories.Admin'), 'icon' => 'icon-cogs'],
                'input' => [[
                    'type' => 'select',
                    'label' => $this->trans('Parent category', [], 'Modules.M4phomecategories.Admin'),
                    'name' => self::CONFIG_PARENT,
                    'desc' => $this->trans('Its subcategories show on the home page as departments, each with its own subcategories. Usually the home category of the shop.', [], 'Modules.M4phomecategories.Admin'),
                    'options' => ['query' => $options, 'id' => 'id_category', 'name' => 'name'],
                ]],
                'submit' => ['title' => $this->trans('Save', [], 'Modules.M4phomecategories.Admin')],
            ],
        ]]);
    }

    public function hookActionFrontControllerSetMedia(): void
    {
        if (($this->context->controller->php_self ?? '') !== 'index') {
            return;
        }
        $base = 'modules/' . $this->name . '/views/';
        $this->context->controller->registerStylesheet('m4p-homecategories', $base . 'css/front.css', [
            'media' => 'all', 'priority' => 150, 'version' => (string) filemtime(__DIR__ . '/views/css/front.css'),
        ]);
    }

    /**
     * The smallest square-ish category image type at least THUMBNAIL_MIN_WIDTH wide, else the largest one;
     * types differ between themes, so none is named here.
     */
    private function thumbnailType(): string
    {
        $types = array_filter(ImageType::getImagesTypes('categories'), static function (array $type): bool {
            return (int) $type['height'] * 2 >= (int) $type['width'];
        });
        usort($types, static function (array $a, array $b): int {
            return (int) $a['width'] <=> (int) $b['width'];
        });
        foreach ($types as $type) {
            if ((int) $type['width'] >= self::THUMBNAIL_MIN_WIDTH) {
                return (string) $type['name'];
            }
        }

        return $types === [] ? ImageType::getFormattedName('small') : (string) end($types)['name'];
    }

    public function hookDisplayHome(): string
    {
        $parentId = (int) Configuration::get(self::CONFIG_PARENT) ?: (int) Configuration::get('PS_HOME_CATEGORY');
        $departments = $this->getDepartments($parentId);
        if ($departments === []) {
            return '';
        }

        $this->context->smarty->assign([
            'm4p_hc_departments' => $departments,
            'm4p_hc_all_link' => $this->context->link->getCategoryLink($parentId),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/categories.tpl');
    }

    /**
     * Active children of the parent with their own active children, thumbnail and product count.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getDepartments(int $parentId): array
    {
        $langId = (int) $this->context->language->id;
        $link = $this->context->link;
        $counts = $this->getProductCounts($parentId);
        $departments = [];
        $thumbnailType = $this->thumbnailType();
        $webp = in_array('webp', explode(',', (string) Configuration::get('PS_IMAGE_FORMAT')), true);

        foreach (Category::getChildren($parentId, $langId, true) as $row) {
            $id = (int) $row['id_category'];
            $image = '';
            $imageWebp = '';
            foreach ([$id . '_thumb', (string) $id] as $name) {
                if (file_exists(_PS_CAT_IMG_DIR_ . $name . '.jpg')) {
                    $image = $link->getCatImageLink($row['link_rewrite'], $name, $thumbnailType);
                    if ($webp && file_exists(_PS_CAT_IMG_DIR_ . $name . '-' . $thumbnailType . '.webp')) {
                        $imageWebp = $link->getCatImageLink($row['link_rewrite'], $name, $thumbnailType, 'webp');
                    }
                    break;
                }
            }
            $departments[] = [
                'id' => $id,
                'name' => $row['name'],
                'link' => $link->getCategoryLink($id, $row['link_rewrite']),
                'image' => $image,
                'image_webp' => $imageWebp,
                'count_label' => $this->countLabel($counts[$id] ?? 0),
                'children' => array_map(
                    fn (array $child): array => [
                        'name' => $child['name'],
                        'link' => $link->getCategoryLink((int) $child['id_category'], $child['link_rewrite']),
                    ],
                    Category::getChildren($id, $langId, true)
                ),
            ];
        }

        return $departments;
    }

    /** "1 produkt", "3 produkty", "5 produktów" – the Polish plural forms; English uses the first two. */
    private function countLabel(int $count): string
    {
        $params = ['%count%' => $count];
        $domain = 'Modules.M4phomecategories.Shop';
        if ($count === 1) {
            return $this->trans('%count% product', $params, $domain);
        }
        $few = in_array($count % 10, [2, 3, 4], true) && !in_array($count % 100, [12, 13, 14], true);

        return $few
            ? $this->trans('%count% products', $params, $domain)
            : $this->trans('%count% products, many', $params, $domain);
    }

    /**
     * Active, visible products per child of the parent, counted once across its whole subtree.
     *
     * @return array<int, int>
     */
    private function getProductCounts(int $parentId): array
    {
        $shopId = (int) $this->context->shop->id;
        $rows = Db::getInstance()->executeS(
            'SELECT top.id_category, COUNT(DISTINCT ps.id_product) AS total
            FROM ' . _DB_PREFIX_ . 'category top
            JOIN ' . _DB_PREFIX_ . 'category c ON c.nleft >= top.nleft AND c.nright <= top.nright
            JOIN ' . _DB_PREFIX_ . 'category_product cp ON cp.id_category = c.id_category
            JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = cp.id_product AND ps.id_shop = ' . $shopId . '
            WHERE top.id_parent = ' . $parentId . ' AND ps.active = 1 AND ps.visibility IN (\'both\', \'catalog\')
            GROUP BY top.id_category'
        );

        $counts = [];
        foreach ((array) $rows as $row) {
            $counts[(int) $row['id_category']] = (int) $row['total'];
        }

        return $counts;
    }
}
