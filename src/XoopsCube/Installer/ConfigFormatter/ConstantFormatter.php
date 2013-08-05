<?php

namespace XoopsCube\Installer\ConfigFormatter;

use XoopsCube\Installer\ValueObject\Site;

class ConstantFormatter implements FormatterInterface
{
    /**
     * Return formatted config text
     * @param  Site   $site
     * @return string
     */
    public function format(Site $site)
    {
        $values = array(
            'XOOPS_MAINFILE_INCLUDED' => '1',
            'XOOPS_ROOT_PATH'         => $site->getRootPath(),
            'XOOPS_TRUST_PATH'        => $site->getTrustPath(),
            'XOOPS_URL'               => $site->getUrl(),
            'XOOPS_SALT'              => $site->getSalt(),
            'XOOPS_DB_TYPE'           => $site->getDB()->getType(),
            'XOOPS_DB_PREFIX'         => $site->getDB()->getPrefix(),
            'XOOPS_DB_HOST'           => $site->getDB()->getHost(),
            'XOOPS_DB_USER'           => $site->getDB()->getUser(),
            'XOOPS_DB_PASS'           => $site->getDB()->getPassword(),
            'XOOPS_DB_NAME'           => $site->getDB()->getName(),
            'XOOPS_DB_PCONNECT'       => $site->getDB()->isPermanentConnectionEnabled() ? 1 : 0,
            'XOOPS_GROUP_ADMIN'       => '1',
            'XOOPS_GROUP_USERS'       => '2',
            'XOOPS_GROUP_ANONYMOUS'   => '3',
        );

        $lines = array();
        $lines[] = '<?php';
        $lines[] = "if (!defined('XOOPS_MAINFILE_INCLUDED')) {";

        foreach ($values as $key => $value) {
            $value = var_export($value, true);
            $lines[] = sprintf("  define('%s', %s);", $key, $value);
        }

        $lines[] = "";
        $lines[] = "  if (!defined('_LEGACY_PREVENT_LOAD_CORE_') && XOOPS_ROOT_PATH != '') {";
        $lines[] = "      include_once XOOPS_ROOT_PATH.'/include/cubecore_init.php';";
        $lines[] = "      if (!isset(\$xoopsOption['nocommon']) && !defined('_LEGACY_PREVENT_EXEC_COMMON_')) {";
        $lines[] = "          include XOOPS_ROOT_PATH.'/include/common.php';";
        $lines[] = "      }";
        $lines[] = "  }";
        $lines[] = "}";

        return implode("\n", $lines);
    }

    /**
     * Return extension
     * @return string
     */
    public function getExtension()
    {
        return 'php';
    }
}
