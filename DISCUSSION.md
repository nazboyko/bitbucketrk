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


