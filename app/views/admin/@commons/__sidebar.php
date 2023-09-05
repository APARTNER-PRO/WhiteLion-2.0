<?php

$cache_key = 'admin_sidebar-' . $this->user->type_id;
$folders = $this->cache->get($cache_key, 'wl_aliases');
if ($folders === NULL) {
    $folders = ['sidebar' => []];
    $rows = $this->db->select('wl_aliases as a', 'id, alias, admin_sidebar', ['&' => '#`admin_sidebar` IS NOT NULL'])
        ->join('wl_ntkd', 'name', $this->data->array_language(['alias_id' => '#a.id', 'content_id' => 0]))
        ->get('array');
    if (!empty($rows)) {
        foreach ($rows as $row) {
            if (!empty($row->admin_sidebar))
                $row->admin_sidebar = unserialize($row->admin_sidebar);
            if (!empty($row->admin_sidebar->sub_items) && $row->admin_sidebar->sub_items == '__get')
                $row->admin_sidebar->sub_items = $this->load->function_in_alias($row->alias, '__sidebar_items', null, 'admin');
            $folder = $row->admin_sidebar->folder ?? 'sidebar';
            if (!empty($row->admin_sidebar->name))
                $row->name = $row->admin_sidebar->name;
            if (empty($row->name))
                $row->name = ucfirst($row->alias);

            $folders[$folder][] = clone $row;
        }
        $order = [];
        foreach ($folders as $key => $folder) {
            foreach ($folder as $item) {
                $order[$key][] = $item->admin_sidebar->order ?? 1;
            }
        }
        foreach ($folders as $key => $folder) {
            array_multisort($order[$key], SORT_DESC, $folders[$key]);
        }
    }
    $this->cache->add($cache_key, $folders, 'wl_aliases');
}

function menu_item($item)
{
    $url = empty($item->admin_sidebar->sub_items) ? SITE_URL . 'admin/' . $item->alias : 'javascript:;';
    $toggle = empty($item->admin_sidebar->sub_items) ? 'data-toggle="ajax"' : ''; ?>
    <div class="menu-item <?= empty($item->admin_sidebar->sub_items) ? '' : 'has-sub' ?>">
        <a href="<?= $url ?>" <?= $toggle ?> class="menu-link">
            <?php if (!empty($item->admin_sidebar->ico)) { ?>
                <div class="menu-icon">
                    <i class="<?= $item->admin_sidebar->ico ?>"></i>
                </div>
            <?php } ?>
            <div class="menu-text"><?= $item->name ?></div>
            <?php if (!empty($item->admin_sidebar->sub_items)) { ?>
                <div class="menu-caret"></div>
            <?php } ?>
        </a>
        <?php if (!empty($item->admin_sidebar->sub_items)) {
            echo '<div class="menu-submenu">';
            foreach ($item->admin_sidebar->sub_items as $uri => $title) {
                $url = SITE_URL . 'admin/' . $item->alias;
                if ($uri != 'index') $url .= '/' . $uri; ?>
                <div class="menu-item">
                    <a href="<?= $url ?>" data-toggle="ajax" class="menu-link">
                        <div class="menu-text"><?= $title ?></div>
                    </a>
                </div>
        <?php }
            echo '</div>';
        } ?>
    </div>
<?php }

if (!empty($folders['sidebar'])) {
    foreach ($folders['sidebar'] as $item) {
        menu_item($item);
    }

    foreach ($folders as $key => $folder) {
        if ($key == 'sidebar')
            continue;
        foreach ($folders['sidebar'] as $item) {
            menu_item($item);
        }
    }
}