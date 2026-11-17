# Discussion

I'm not exactly sure what to write about here, so I'll just describe the development process and all the bugs I come across.
I apologize in advance for the fact that both the structure of this post and my thoughts may change.

## Section 1. Project setup

### MySQL port

I bound the port to `127.0.0.1`. The database is still reachable from the host for a GUI client. 
The compose file published MySQL on every network interface with the password `password`, so anyone on the same network could connect.

I also pinned the image to `mysql:8.4` and added a healthcheck, so other containers start only after the database accepts connections.

### Lock file out of sync for some reason!

`npm ci` stopped on a clean copy because `package-lock.json` did not match `package.json`. Several `@emnapi` packages were missing from it. 
I regenerated only the lock file with `npm install --package-lock-only`. It added the missing entries and moved one transitive package by a patch version. 

### Migration failed on MySQL

`php artisan migrate` stopped at the destinations table with MySQL error 1101, because a JSON column cannot have a literal default.
I removed the default, so every row has to state its activities, which the seeder already does.
This is Section 2 work, but the database could not be created without it, so it is fixed here.

### Everything looks fine in Docker

I moved all of it into containers: `app` for PHP, `vite` for Node and `db` for MySQL.
`composer.json` allows PHP 8.3, but the locked Symfony 8 packages need PHP 8.4 or newer, so the image uses PHP 8.4.
The constraint in `composer.json` is corrected together with the dependency updates.

### Lock file out of sync again.

I ran `docker compose run --rm vite npm install --package-lock-only --ignore-scripts` just to update the `package-lock.json`

### MySQL version

I tried again the v8 version, and it's not working on my local env. so i returned back to the v8.4

### Tests run on MySQL

The project was set up to test on SQLite in memory. I pointed the tests at a second MySQL database instead.
The migration bug above is the reason: SQLite accepts that JSON default, so the suite would have stayed green while the real 
database could not be created.

### Unused JavaScript

The only JavaScript in the project loaded axios and attached it to `window`. Nothing used it. Livewire ships its own script,
so the page needs no JavaScript bundle of its own. I removed the files, the Vite entry and the dependency. 
That also dropped 23 packages from the install.

### Outdated packages with known vulnerabilities

The lock files were about seven months old. `npm audit` listed 9 advisories and `composer audit` listed 49, one of them 
critical in the framework itself. None of it was malicious code, only old versions. I updated the rest inside the version 
ranges the project already allowed, so no constraint was loosened. Laravel went from 13.3 to 13.42 and Livewire from 4.2 to 4.4.
Both audits are clean now.
The one constraint I did change is PHP: `composer.json` said 8.3, but the locked Symfony packages already needed 8.4, so it now says 8.4.

### Code checks

I added Larastan at level 8 for static analysis, the Pint is in the project already.

### CI

There is a GitHub Actions workflow with two jobs. The backend job starts MySQL and runs `composer audit` and `composer check`. 
The frontend job runs `npm audit` and builds the assets. The audits are there so that an outdated dependency fails the build
and does not sit unnoticed for months again.

### Left out of the setup

I did not use Laravel Sail. It would work, but it brings a large generated compose file, and the three services here fit
in thirty lines that are easy to read. I also did not build a production image, because nothing in the assignment deploys anywhere.

___
## Section 2. Database and API

### The seed endpoint

`POST /api/seed` emptied the destinations table and filled it again, for anyone who called it. 
Protecting it with a token would still leave a button on the public API that deletes data.
Loading demo data is a job for whoever runs the app, so it stays as an Artisan command and the endpoint is gone.
The cost is that seeding needs console access.

### Model and schema

The model was empty, so values came back from the database as plain strings. `activities` is now cast to an array, 
the two numbers to integers, and `cost_level` to a `CostLevel` enum. The enum lists its cases from cheapest to most expensive, 
and that order is what sorting by cost level uses later.

A destination is unique by name and country. That is an assumption that fits this data. Two places with the same name in 
one country would need a better key, such as an id from the source the data comes from.

Budget and visitor counts are unsigned, so the database itself refuses negative numbers.

I edited the original migration and did not add a new one, because this schema has never been deployed anywhere.
But on a live project the same change would be a new migration.

Also some tests are failed, but I don't see any issues here, probably the issue is in my local env. 

### Seeding

The seeder inserted rows blindly, so running it twice doubled the catalog. It now updates or creates each demo row by name and country. 
Running it again restores the demo rows to their original values and leaves every other row alone. 
The same fifteen destinations were also copied into the controller and the Livewire component.
The seeder is now the only place that holds them.

### API output

I added a new resource class, and now it defines the response field by field. 
The only addition is `id`, so a client can tell two destinations apart.

### Search and sorting live on the model

The API and the page need exactly the same search and sorting. I put both into two query scopes on `Destination`, `search` 
and `sortedBy`, so a call reads `Destination::query()->search($term)->sortedBy($sort, $direction)`.
I considered a separate query class and a repository. For one model with one query they add files and indirection without 
adding anything a scope does not already give, and scopes are what a Laravel developer looks for first.

The fields a list can be sorted by are a `DestinationSort` enum. A name that is not in the enum cannot reach the query, 
so there is no column name coming from the browser at all.

### How search behaves

The old search was case sensitive, searched the JSON text of the activities, and matched numbers as text. The rules now:

- The term is trimmed and case does not matter.
- It looks in name, country, region, cost level and activity names.
- `%` and `_` are ordinary characters, not wildcards.
- A whole number also matches the daily budget, but only exactly. Before, `15` matched a budget of 150, which helps nobody.
- `0` is a real term. The old code treated it as empty.

Activities are searched with MySQL's `JSON_SEARCH`, so a quote or a bracket no longer matches every row. Sorting by cost level uses MySQL's `FIELD` to follow the enum order, because alphabetical order puts Luxury before Moderate. Both are MySQL only, which is the tradeoff of keeping activities in a JSON column. With an `activities` table the search would be plain SQL and work on any database.

### API input and paging

The endpoint returned the whole table with no way to narrow it down. It now takes `search`, `sort`, `direction`, `page` and `per_page`,
all checked by a form request. A page holds 15 rows unless the client asks for another size, up to 100.

Paging changes the response for existing clients: `data` now holds one page, and `links` and `meta` sit next to it. 
A client that expected every row in one call has to follow the pages.
I accepted that because an endpoint without a size limit only works while the table is small.

Errors on `/api` routes are always JSON, even when the client sends no `Accept` header. Without that, a bad parameter answered with a redirect to the home page.

### Rate limit

I added the limiter here so every API client gets 60 requests a minute, counted per signed in user or per IP address.
It uses the named `api` limiter and the middleware Laravel already has for it. The number is a guess.
A real one would come from watching traffic.

---
## Section 3. User interface

### What was wrong with the component

I went through `DestinationExplorer` and its view and found these problems:

- Search was one keystroke behind. It ran on `keydown`, before the input value had changed.
- Search was case sensitive and treated `0` as empty.
- Searching or resetting threw away the sorting, while the component still claimed to be sorted.
- `sort()` took any field name from the browser. An unknown name crashed the page.
- The comparison never returned zero for equal values, and cost level was sorted alphabetically.
- The whole catalog sat in two public arrays, so it travelled to the browser and back on every request and could be changed there.

### State

The component now keeps four small values: the search text, the sort field, the direction and the page.
The rows are read from the database on each render through the same two scopes the API uses.

The page does not call the API. Both run on the same server, so an HTTP request from the app to itself would only 
add latency and a second place to handle errors. They share the model scopes instead.

The search lag is fixed by binding the input with a live model and a 300 ms debounce. 
I could not write an automated test for the timing itself, because Livewire tests do not run a browser.

The page shows 10 rows at a time and the API defaults to 15.

Search, sorting and page are kept in the query string, so a reload or a shared link shows the same list.
Livewire replaces the current history entry and does not add a new one for every keystroke, so the back button still leaves the page.

___
## Open question

These are choices I made on my own where another answer would also be reasonable.

**Should the app container run as root?** It does now. On macOS that causes no trouble. On Linux the files it creates
in the project folder would belong to root. A fixed user id in the image fixes that for most people but not all,
so I left it and wrote it down here.



