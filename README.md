# Nolan CellType for ExpressionEngine 2.0 Matrix Field

### Overview

Nolan enables a very simple matrix style field within a [Pixel & Tonic Matrix](http://pixelandtonic.com/matrix) field, native Grid field or as a stand alone native EE custom field.

![Field Output](http://f.cl.ly/items/3x2y3w2K3P1n2e1y0l31/Image%202014.01.10%203%3A56%3A48%20PM.png)

### Installing/Usage
Install the Nolan Fieldtype and you'll see 'Nolan' as an available celltype when configuring Matrix or Grid.

Two required configuration fields are Column Labels and Column Names.

Optional configuration fields are Column Field Types, and Maximum rows.

'Column Labels' are what your publishers see, and 'Column Names' are what you reference in templates. Column field types allow you to use text inputs (default), textareas, and checkboxes.

For example, I have a Nolan cell with a short name of 'players', and I want it to contain first and last names of team players. I also want to include a short bio, and also indicate if that player is a Star Player.

In Column Labels, I'd enter: **First Name | Last Name | Bio | Star Player** <br />
In Column Names, I'd enter: **first_name | last_name | bio | star_player**<br />
In Column Field Types, I'd enter **text_input | text_input | textarea | checkbox**

Given this is a five aside tournament, I want to restrict the number of Nolan rows available to 5 also.

![Configuration](http://f.cl.ly/items/3C390s311L3z0D2R1U0G/Image%202014.01.10%203%3A44%3A44%20PM.png)

Then if my matrix field had the short name 'teams', I could access the Nolan fields like so:

	{teams} <!-- matrix field -->
		{players}<!-- nolan cell -->
			<h4{if star_player} class="star-player"{/if}>{first_name}, {last_name} - <em>{position}</em><h4>
			{exp:nolan:format text_format="xhtml"}{bio}{/exp:nolan:format}
		{/players}
	{/teams}

Note here that the **{bio}** field is being parsed with xhtml typography using the Nolan plugin syntax.

Additional variables available are:

	{total_nolan_cols} - Number of columns in the Nolan cell
	{total_nolan_rows} - Number of rows in the Nolan cell
	{nolan_row_count} - Same as {count} to avoid variable clashes

Parameters

	limit="3" - limit the returned rows
	backspace="1" - remove end characters from the final output
	offset="1" - skip initial rows

## Typography styling

Nolan comes bundled with a plugin which allows you to add typography parsing to your Nolan Fields.

In the example above, our **{bio}** field needs to have xhtml parsing applied to convert double line breaks to paragraphs. We can call the Nolan plugin to do this for us.

	{exp:nolan:format text_format="xhtml"}{bio}{/exp:nolan:format}

Acceptable values for the text_format parameter are:

* xhtml - full parsing similar to an xhtml custom field
* br - similar to an Auto BR custom field
* lite - same as ExpresssionEngine entry Title fields (basically xhtml without the paragraph html wrappers)

* * *

### Caveats
Changing a Nolan cells short name will **not** update existing values set, so choose your short names wisely.

### Support and Feature Requests
This celltype was developed for a specific task and has been 'generalised' for GitHub. I haven't done a whole lot of testing so be aware if you're using in a production environment.

The add-on is not officially supported but send requests/bug reports/pull requests here to this repo - NOT to devot:ee as I won't get notified. 

### Hat tip

Hat tip to Stephen Lewis [@monooso](http://twitter.com/monooso) of Experience Internet for [Roland.js](https://github.com/experience/jquery.roland.js) which I modified with permission for Nolan.

### Hooks

#### `nolan_pre_parse`

Alter the field data before rendering the custom field tag pair in an `{exp:channel:entries}` tag.

```
if (ee()->extensions->active_hook('nolan_pre_parse'))
{
	// map of col short name => col type
	$col_types = array_combine(
		$this->get_col_attributes('nolan_col_names'),
		$this->get_col_attributes('nolan_col_types')
	);

	$data = ee()->extensions->call('nolan_pre_parse', $data, $tagdata, $this->settings, $col_types);
}

$r = ee()->TMPL->parse_variables($tagdata, $data);
```

Example:

```
public function nolan_pre_parse($data, $tagdata, $settings, $col_types)
{
    if (ee()->extensions->last_call !== FALSE)
    {
        $data = ee()->extensions->last_call;
    }

    foreach ($data as $i => $row)
    {
        foreach ($row as $key => $value)
        {
            if (isset($col_types[$key]) && $col_types[$key] === 'contact_email')
            {
                $data[$i][$key] = '<a href="mailto:'.$value.'">Contact</a>';
            }
        }
    }

    return $data;
}
```

#### `nolan_custom_col_type_XXX`

Render a custom col type.

```
return ee()->extensions->call('nolan_custom_col_type_'.$type, $field_name, $cell_data, $vars);
```

Example:

```
public function nolan_custom_col_type_email($field_name, $cell_data, $vars)
{
    return form_input(array(
        'type' => 'email',
        'name' => $field_name,
        'value' => $cell_data,
        'class' => 'email',
        'placeholder' => 'Enter a valid email',
    ));
}
```

* * *

Copyright (c) 2012 Iain Urquhart
http://iain.co.nz