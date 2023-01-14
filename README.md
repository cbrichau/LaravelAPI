**This is my very first Laravel app, made in 1 week of learning.**

Refer to the [Wiki](https://github.com/cbrichau/LaravelAPI/wiki) to get started, or keep reading to know more about the project's content.

- [TL;DR](#tldr)
- [Tech stack](#tech-stack)
- [Exercise](#exercise)
   - [Context](#context)
   - [Task](#task)
- [Solution](#solution)
- [Learnings & Next steps](#learnings--next-steps)

# TL;DR

In short, this project is an API made with Laravel and a bunch of packages. It provides, for a fictive eshop:
- user authentication
- adding/removing products to/from a user's basket
- generating CSV files containing basket removal statistics.

# Tech stack

* The **Laravel** framework with its local environment using **Laravel Sail** and customised **Docker Compose** (I added phpMyAdmin).
* Extra information on the local environment with **Laravel Telescope**.
* Testing with **PHPUnit**.
* Static analysis with **PHPStan (Larastan)** on max level.
* Coding standards enforcement with **PHP-CS-Fixer**.
* A **REST** JSON API.
* A **GitHub Actions** workflow that runs PHPStan and PHPUnit.
* Swagger and [Wiki](https://github.com/cbrichau/LaravelAPI/wiki) **documentation**.

# Exercise

### Context

You're part of a team working on an **eshop's backend API** that handles (among other things):
- adding and removing products to a customer's basket
- checking out that basket
- and restricting these actions to the **authenticated user** the basket belongs to.

The sales team wants to know **what products were added to a basket, but removed before checkout**. They also want to dig into _why_ on a regular basis using a spreadsheet tool such as Excel or Google Sheets.

> Note that a product is either sold or lost, never both. If a product is added to the basket, then removed, then added again, it doesn't count as a loss anymore.

### Task

Your job is to **create an endpoint** that:
- enables the sales team to **get this data** (i.e. the product removals)
- supports **filters** (e.g. in the last 30 days) so users don't have to download the whole thing each time
- **returns a CSV** file compatible with their spreadsheets
- is only **accessible to internal users** (i.e. the employees of the sales team).

You're expected to do a good job, i.e. produce code and documentation that is clean, stable, and supports teamwork.

# Solution

I created 4 endpoints to **set the scene**:

1. `sign-up` enables the eshop to **register a new user**.
2. `sign-in` enables the eshop to **log in a user**.
3. `remove-item` enables the eshop to **remove an item from a basket**.
4. `add-item` enables the eshop to **add an item to a basket**.

**To deliver the new feature**, I added functionality to 3 & 4 and created the 5th endpoint :

3. `remove-item` now marks a product as lost, rather than actually taking it out of the basket.
4. `add-item` now either adds a new product or, if it was previously added then removed, simply "un-looses" it.
5. `download-losses` provides information about the losses. It supports filters and triggers a CSV file download directly in the browser.

I also used **factories and seeders** to generate sample data to play with.

Finally, I added various packages to:

- **document** these endpoints, with their success/error responses and potential payloads (Swagger)
- run **tests** (PHPUnit) and **static analysis** (PHPStan)
- enforce **coding standards** (PHP-CS-Fixer)
- provide **local requests insights** for debugging (Telescope)
- enable a **CI pipeline** (GitHub Actions).

> Note that this reflects the thought process, not the actual implementation steps. For those, please refer to the [commit history](https://github.com/cbrichau/LaravelAPI/commits/main).

# Learnings & Next steps

With these first steps with Laravel, **I learned about:**
- models
- migrations
- factories
- seeders
- routes
- middlewares
- controllers
- authentication (sanctum)
- adding packages
- Sail configuration
- tests.

**Next, I want to find out about:**
- error handling using form requests, validation routines, and exceptions
- API resources for forming JSON responses
- file storage
- header-based API versioning
- querying the database with Eloquent
- using factories to build test payloads
- overall, splitting up the logic more instead of having it all in the controllers.