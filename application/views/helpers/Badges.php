<?php
/* Icinga Web 2 Top Level View | (c) 2017 Icinga Development Team | GPLv2+ */

use Icinga\Module\Toplevelview\Tree\TLVStatus;
use Icinga\Module\Toplevelview\Util\Str;

class Zend_View_Helper_Badges extends Zend_View_Helper_Abstract
{
    /** @var \Icinga\Web\View */
    public $view;

    /**
     * badges renders a TLVNode's TLVStatus into HTML.
     * A badge represents the number of status of a given node
     */
    public function badges(TLVStatus $status, $problemsOnly = true, $showTotal = false)
    {
        $htm = '';

        $values = false;
        $htm .= '<div class="badges">';
        foreach ($status->getProperties() as $key => $value) {
            if ($problemsOnly === true && ($key === 'ok' || $key === 'downtime_active')
                || ($key === 'total' && $showTotal !== true)
            ) {
                continue;
            }
            if ($value !== null && $value > 0) {
                $values = true;
                $title = $value . ' ' . Str::prettyTitle($key);
                $class = 'tlv-status-badge ' . str_replace('_', ' ', $key);
                $htm .= sprintf(
                    '<div class="badge status-badge %s" title="%s">%s</div>',
                    $class,
                    $title,
                    $value
                );
            }
        }
        $htm .= '</div>';

        return $values ? $htm : '';
    }
}
