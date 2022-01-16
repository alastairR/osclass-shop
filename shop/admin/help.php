<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');
/*
 *      OSCLass – software for creating and publishing online classified
 *                           advertising platforms
 *
 *                        Copyright (C) 2010 OSCLASS
 *
 *       This program is free software: you can redistribute it and/or
 *     modify it under the terms of the GNU Affero General Public License
 *     as published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful, but
 *         WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Affero General Public License for more details.
 *
 *      You should have received a copy of the GNU Affero General Public
 * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>

<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 0 20px 20px;">
        <div>
            <fieldset>
                <legend>
                    <h1><?php _e('shopping cart Help', 'shoppingcart') ; ?></h1>
                </legend>
                <h2>
                    <?php _e('What is shopping cart Plugin?', 'shoppingcart') ; ?>
                </h2>
                <p>
                    <?php _e('shopping cart plugin allows you to display a link that will allow user to save items on a shoppingcart page', 'shoppingcart') ; ?>.
                </p>
                <h2>
                    <?php _e('How does shopping cart plugin work?', 'shoppingcart') ; ?>
                </h2>
                <p>
                    <?php _e('In order to use shopping cart plugin, you should edit your theme files and add the following line anywhere in the code you want the shopping cart link to appear', 'shoppingcart') ; ?>:
                </p>
                <pre>
                    &lt;?php shoppingcart(); ?&gt;
                </pre>
                <h2>
                    <?php _e('Could I cutomize the style of shopping cart plugin?', 'shoppingcart') ; ?>
                </h2>
                <p>
                    <?php _e("Of course you can. shopping cart display a link only you can use css to make a button or anything else", 'shoppingcart') ; ?>.
                </p>
                <h2>
                    <?php _e('Did shopping cart plugin work with all version of OSClass?', 'shoppingcart') ; ?>
                </h2>
                <p>
                    <?php _e("In order to work this pluggin need OSClass v2.2 and up without this version pluggin will crash", 'shoppingcart') ; ?>.
                </p>
                <p>
                    <?php printf(__('You have %s version', 'shoppingcart'), OSCLASS_VERSION); ?>.
                </p>
            </fieldset>
        </div>
    </div>
</div>
