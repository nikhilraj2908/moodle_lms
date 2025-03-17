# Moodle Cards format
A format which displays sections of your courses as cards with images, similar to `format_grid` or `format_tiles`.
The cards format is an extension of the default Topics format to make it easier to maintain.

## Features
- Large cards with user configurable background images
- Responsive cards scale down for mobile devices 
- Choose between horizontal and vertical cards, or square grids
- Cards can display a user's completion progress for each section
- Easily import images used in the grid format

## Installation
The cards format requires Moodle 4.0 or later, and for the default topics format to be present on your site.
There's no special installation steps or instructions, just install it as you would any other plugin

### Install from Moodle.org
- Download .zip file from https://moodle.org/plugins/format_cards
- Navigate to `/moodle/root/course/format`
- Extract the .zip to the current directory
- Go to your Moodle admin control panel, or `php /moodle/root/admin/cli/upgrade.php`

### Install from git
- Navigate to your Moodle root folder
- `git clone https://github.com/Vidalia/moodle-format_cards.git course/format/cards`
- Make sure that user:group ownership and permissions are correct
- Go to your Moodle admin control panel, or `php /moodle/root/admin/cli/upgrade.php`

### Install from .zip
- Download .zip file from GitHub
- Navigate to `/moodle/root/course/format`
- Extract the .zip to the current directory
- Rename the `moodle-format_cards-master` directory to `cards`
- Go to your Moodle admin control panel, or `php /moodle/root/admin/cli/upgrade.php`
