# Navigator

Navigator is a fluent interface to WordPress

## Todo

-   [x] Auth
    -   [ ] Policies/gates
-   [ ] Broadcasting
-   [ ] Bus
-   [x] Cache
-   [x] Collections
-   [ ] Conditionable
-   [x] Config
-   [x] Console
-   [x] Container
-   [x] Contracts
-   [ ] Cookie
-   [ ] Database
    -   [ ] Attachment model
    -   [x] User capabilities
-   [x] Encryption
-   [x] Events
-   [ ] Faker
    -   [ ] WP faker provider
-   [x] Filesystem
-   [x] Foundation
-   [x] Hashing
-   [x] Http
-   [ ] Log
-   [ ] Macroable
-   [x] Mail
-   [x] Notifications
-   [ ] Number @inprogress
-   [x] Pagination
-   [ ] Pipeline
-   [ ] Process
-   [x] Queue
-   [ ] Redis
-   [x] Routing
-   [x] Schedule
-   [x] Session
-   [ ] Support
-   [ ] Testing
-   [ ] Translation
-   [x] Validation
-   [x] View

## Additional todo

-   [ ] Move helpers.php into theme
-   [ ] Create theme repo
-   [ ] Navigator as composer package
-   [ ] use reflection in container
-   [ ] use attributes e.g. listeners priority
-   [ ] set relevent classes to readonly and final


## Container todo

The container has been expanded to include a make method (that injects dependencies in constructors) and a call method (that injects dependencies in methods and functions). The next task is to update all services where useful to use make or call instead of how they are currently resolving dependencies (usually passed directly). This will be most useful in controllers and listeners so the user can specify the dependencies on controller constructors or in route methods.

- [ ] Router: route class constructor
- [ ] Router: route method
- [ ] Events: Listener constructor
- [ ] Validator
