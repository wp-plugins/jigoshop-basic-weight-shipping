=== Jigoshop Basic Weight Shipping ===
Contributors: linus1881
Plugin URI: http://www.intervisio.ch/webwork/wordpress-plugins/
Author URI: http://www.intervisio.ch/
Tags: jigoshop, ecommerce, shipping, bundle, method
Requires at least: WordPress 3.2.1 and Jigoshop 1.6.2
Tested up to: WP 3.5.1 and Jigoshop 1.7
Stable tag: 1.1.0
License: GPLv2 or later

Extend the Jigoshop shipping methods by adding a basic weight method. 


== Description ==

Extend the Jigoshop shipping methods by adding a basic weight method.

This is a very simple plugin that allows you to set weight based shipping fees. Five levels of weights are alowed.
Example: level1 = 2kg with next field as 10 CHF = the total weight of an order in the cart is between 0kg and 2 kg and has a fee of 10 CHF.
Example: level2 = 5 kg with next field as 20 CHF = the total weight of an order in the cart is between 2kg and 5 kg and has a fee of 20 CHF.
etc.

My thanks goes to Iain at Polevaultweb, who developed a basic bundle shipping plugin for jigoshop. You can find his plugin at http://jigoshop.com/product/basic-bundle-shipping/


[Plugin Page](http://www.intervisio.ch/webwork/wordpress-plugins/) | [@gasserol](http://www.twitter.com/gasserol/) | 

== Installation ==

This section describes how to install the plugin and get it working.

You can use the built in installer and upgrader, or you can install the plugin manually.

1. Delete any existing `jigoshop-basic-weight-shipping` folder from the `/wp-content/plugins/` directory
2. Upload `jigoshop-basic-weight-shipping` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to the Shipping panel under the Jigoshop 'Settings' menu.

If you have to upgrade manually simply repeat the installation steps and re-enable the plugin.

== Changelog ==

= 1.1.0 =

* Modified the calculation part to include vat calculation on shipping fees (with option to enable this calculation or to disable it

* Added Handling fee field and calculation of shipping total

* Deactivated backwards compatibility to Jigoshop versions prior and included 1.4



= 1.0.0 =

* Initial adaption

== Frequently Asked Questions ==

= I have an issue with the plugin =

Please contact us via email on Contact form on [Support Forum](http://www.intervisio.ch/webwork/wordpress-plugins/).