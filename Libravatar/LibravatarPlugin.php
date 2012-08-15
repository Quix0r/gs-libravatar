<?php
/*
 * Libravatar plugin for Status.net
 * Copyright (C) 2012 Melissa Draper <melissa@catalyst.net.nz>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package LibravatarPlugin
 * @maintainer Melissa Draper <melissa@catalyst.net.nz>
 */

if (!defined('STATUSNET') && !defined('LACONICA')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

class LibravatarPlugin extends Plugin
{
    function onEndProfileGetAvatar($profile, $size, &$avatar)
    {
        if (empty($avatar)) {
            $user = $profile->getUser();
            if (!empty($user) && !empty($user->email)) {
                // Fake one!
                $avatar = new Avatar();
                $avatar->width = $avatar->height = $size;
                $avatar->url = $this->libravatar_url($user->email, $size);
                return false;
            }
        }

        return true;
    }

    function libravatar_url($email, $size)
    {
        try {
            if (stream_resolve_include_path('Services/Libravatar.php')) {
                include_once 'Services/Libravatar.php';
            }
        }
        catch (Exception $e) {
            // It doesn't matter
        }
        if (!class_exists('Services_Libravatar')) {
            // TRANS: Libravatar library missing exception.
            throw new Exception(_m('The PEAR Services_Libravatar library is required for the Libravatar plugin.'));
        }

        $libravatar = new Services_Libravatar();
        $libravatar->setSize($size)
                   ->setDefault(Avatar::defaultImage($size))
                   ->setHttps(true);
        $url = $libravatar->getUrl($email);

            return $url;
    }

    function onPluginVersion(&$versions)
    {
        $versions[] = array('name' => 'Libravatar',
                            'version' => STATUSNET_VERSION,
                            'author' => 'Melissa Draper, Eric Helgeson, Evan Prodromou',
                            'homepage' => 'http://status.net/wiki/Plugin:Libravatar',
                            'rawdescription' =>
                            // TRANS: Plugin decsription.
                            _m('The Libravatar plugin allows users to use their <a href="http://www.libravatar.org/">Libravatar</a> with StatusNet.'));

        return true;
    }
}