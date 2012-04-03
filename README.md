# Nolan CellType for ExpressionEngine 2.0 Matrix Field

### Overview

Nolan enables a very simple matrix style field within a Pixel & Tonic Matrix field

### Installing/Updating
Install the Nolan Fieldtype and you'll see 'Nolan' as an available celltype when configuring Matrix.

Two required configuration fields are Column Labels and Column Names.

Labels are what your publishers see, and labels are what you reference in templates.

For example, I maybe have a Nolan cell with a short name of 'players', and I want it to contain first and last names of team players.

In Column Labels, I'd enter: [b]First Name | Last Name[/b]
In Column Names, I'd enter: [b]first_name | last_name[/b]

Then if my matrix field had the short name 'teams', I could access the Nolan fields like so:

	{teams} <!-- matrix field -->
		{players}<!-- nolan cell -->
			{first_name}, {last_name}
		{/players}
	{/teams}


### Documentation
A work in progress.

#### Screenshots:

Configuration:
![Configuration](http://iain.co.nz/dev/nolan_config.png)

Field/Cell:
![Field Output](http://iain.co.nz/dev/nolan_field.png)

### Support and Feature Requests
Not officially supported but send requests/bug reports/pull requests here to this repo - NOT to devot:ee as I won't get notified. 

Copyright (c) 2012 Iain Urquhart
http://iain.co.nz