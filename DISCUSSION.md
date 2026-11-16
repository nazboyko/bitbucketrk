# Discussion

I'm not exactly sure what to write about here, so I'll just describe the development process and all the bugs I come across.
I apologize in advance for the fact that both the structure of this post and my thoughts may change.

## Section 1. Project setup

### MySQL port

I bound the port to `127.0.0.1`. The database is still reachable from the host for a GUI client. 
The compose file published MySQL on every network interface with the password `password`, so anyone on the same network could connect.

I also pinned the image to `mysql:8.4` and added a healthcheck, so other containers start only after the database accepts connections.
