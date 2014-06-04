# Root WordPress Framework

Root WordPress Framework formatted as *Starter Theme*. Visit the [official website](http://rootwp.com/).


# How to use

Upload the project files into themes path `wp-content/themes`, like a normal WordPress Theme.


# Improve your Theme

If you already have a theme ready and want to add the functionality to it Root, just upload the folder /lib in the root folder of your theme and perform a simple require:

    require_once TEMPLATEPATH . '/lib/root.php';`

When calling the initializer framework, all other files will be included when necessary. Thinking of better organize your code, you can make use of the hook root_setup to start the settings of WordPress, called hooks, etc.

    add_action( 'root_setup', 'custom_setup' );

    function custom_setup()
    {
        // insert your personalized code here
    }

# Pay attention

Don't change the `/lib` folder and your content!


# Contribute

Create a branch by clicking at *Fork* button, edit the code and then submit a *Pull Request* to the project.
Use *issues* to report bugs or send suggestions.


# Copyright and license

Copyright 2013-2014 - [Kodame](http://kodame.com.br)
This project is licensed with GPLv2.
