# Nolan CellType for ExpressionEngine 2.0 Matrix Field

### Overview

Nolan enables a very simple matrix style field within a [Pixel & Tonic Matrix](http://pixelandtonic.com/matrix) field

![Field Output](http://iain.co.nz/dev/nolan_field.png)

### Installing/Usage
Install the Nolan Fieldtype and you'll see 'Nolan' as an available celltype when configuring Matrix.

Two required configuration fields are Column Labels and Column Names.

'Column Labels' are what your publishers see, and 'Column Names' are what you reference in templates.

For example, I maybe have a Nolan cell with a short name of 'players', and I want it to contain first and last names of team players along with their position in the game.

In Column Labels, I'd enter: **First Name | Last Name | Position** <br />
In Column Names, I'd enter: **first_name | last_name | position**

![Configuration](http://iain.co.nz/dev/nolan_config.png)

Then if my matrix field had the short name 'teams', I could access the Nolan fields like so:

	{teams} <!-- matrix field -->
		{players}<!-- nolan cell -->
			{first_name}, {last_name} - {position}
		{/players}
	{/teams}

Additional variables available are:

	{total_nolan_cols} - Number of columns in the Nolan cell
	{total_nolan_rows} - Number of rows in the Nolan cell
	{nolan_row_count} - Same as {count} to avoid variable clashes


### Caveats
Changing a Nolan cells short name will **not** update existing values set, so choose your short names wisely.

### Support and Feature Requests
This celltype was developed for a specific task and has been 'generalised' for GitHub. I haven't done a whole lot of testing so be aware if you're using in a production environment.

The add-on is not officially supported but send requests/bug reports/pull requests here to this repo - NOT to devot:ee as I won't get notified. 

### Hat tip

Hat tip to Stephen Lewis [@monooso](http://twitter.com/monooso) of Experience Internet for [Roland.js](https://github.com/experience/jquery.roland.js) which I modified with permission for Nolan.

* * *

Copyright (c) 2012 Iain Urquhart
http://iain.co.nz