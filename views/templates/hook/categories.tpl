{**
 * m4p_homecategories
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 *}
<section class="m4p-hc" aria-labelledby="m4p-hc-title">
  <div class="m4p-hc__head">
    <h2 class="m4p-hc__title" id="m4p-hc-title">{l s='Catalogue' d='Modules.M4phomecategories.Shop'}</h2>
    <a class="m4p-hc__all" href="{$m4p_hc_all_link|escape:'html':'UTF-8'}">
      {l s='Whole catalogue' d='Modules.M4phomecategories.Shop'}
      <svg class="m4p-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
    </a>
  </div>

  <div class="m4p-hc__grid">
    {foreach from=$m4p_hc_departments item=department}
      <div class="m4p-hc__cell">
        <h3 class="m4p-hc__name"><a href="{$department.link|escape:'html':'UTF-8'}">{$department.name|escape:'html':'UTF-8'}</a></h3>
        {if $department.image}
          <picture class="m4p-hc__pic">{if $department.image_webp}<source srcset="{$department.image_webp|escape:'html':'UTF-8'}" type="image/webp">{/if}<img class="m4p-hc__thumb" src="{$department.image|escape:'html':'UTF-8'}" width="88" height="88" alt="" loading="lazy"></picture>
        {/if}
        {if $department.children}
          <ul class="m4p-hc__subs">
            {foreach from=$department.children item=child}
              <li><a href="{$child.link|escape:'html':'UTF-8'}">{$child.name|escape:'html':'UTF-8'}</a></li>
            {/foreach}
          </ul>
        {/if}
      </div>
    {/foreach}
  </div>

  <div class="m4p-hc__list">
    {foreach from=$m4p_hc_departments item=department name=departments}
      <details class="m4p-hc__item"{if $smarty.foreach.departments.first} open{/if}>
        <summary class="m4p-hc__summary">
          {if $department.image}
            <picture class="m4p-hc__pic">{if $department.image_webp}<source srcset="{$department.image_webp|escape:'html':'UTF-8'}" type="image/webp">{/if}<img class="m4p-hc__thumb" src="{$department.image|escape:'html':'UTF-8'}" width="56" height="56" alt="" loading="lazy"></picture>
          {else}
            <span class="m4p-hc__thumb" aria-hidden="true"></span>
          {/if}
          <span class="m4p-hc__label">
            <b>{$department.name|escape:'html':'UTF-8'}</b>
            <small>{$department.count_label|escape:'html':'UTF-8'}</small>
          </span>
          <svg class="m4p-icon m4p-hc__chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6"/></svg>
        </summary>
        <ul class="m4p-hc__sublist">
          <li><a href="{$department.link|escape:'html':'UTF-8'}">{l s='Everything in this department' d='Modules.M4phomecategories.Shop'}</a></li>
          {foreach from=$department.children item=child}
            <li><a href="{$child.link|escape:'html':'UTF-8'}">{$child.name|escape:'html':'UTF-8'}</a></li>
          {/foreach}
        </ul>
      </details>
    {/foreach}
  </div>
</section>
