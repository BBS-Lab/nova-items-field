<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default field options
    |--------------------------------------------------------------------------
    |
    | These values are used as the defaults for every Items field. They can all
    | be overridden per-field through the fluent API (e.g. ->chips(), ->max()).
    |
    */

    // Where the "add" control sits relative to the list: 'top' or 'bottom'.
    'add_button_position' => 'bottom',

    // Allow drag-and-drop reordering of the items.
    'draggable' => false,

    // Render the items as chips/tags instead of structured rows.
    'chips' => false,

    // The HTML input type used for each item ('text', 'number', 'email', ...).
    'input_type' => 'text',

];
