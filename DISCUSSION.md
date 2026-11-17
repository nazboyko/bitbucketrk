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




