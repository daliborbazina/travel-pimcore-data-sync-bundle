# Decisions

## Sections

- [Goal](#goal)
- [Main design choices](#main-design-choices)
    - [1. Use console commands as entry points](#1-use-console-commands-as-entry-points)
    - [2. Use file-based JSON feeds](#2-use-file-based-json-feeds)
    - [3. Keep catalog sync and availability sync separate](#3-keep-catalog-sync-and-availability-sync-separate)
    - [4. Map external input into DTOs first](#4-map-external-input-into-dtos-first)
    - [5. Validate before persisting](#5-validate-before-persisting)
    - [6. Use narrower DTOs for Pimcore persistence](#6-use-narrower-dtos-for-pimcore-persistence)
    - [7. Let sync services do the orchestration](#7-let-sync-services-do-the-orchestration)
    - [8. Keep Pimcore access behind repositories](#8-keep-pimcore-access-behind-repositories)
    - [9. Keep relation assignment separate](#9-keep-relation-assignment-separate)
    - [10. Support dry-run for sync commands](#10-support-dry-run-for-sync-commands)
    - [11. Use fail-fast handling for unexpected sync errors](#11-use-fail-fast-handling-for-unexpected-sync-errors)
    - [12. Build a publish preview instead of a full export integration](#12-build-a-publish-preview-instead-of-a-full-export-integration)
    - [13. Keep Pimcore usage data-centric](#13-keep-pimcore-usage-data-centric)
- [What this repository is not trying to be](#what-this-repository-is-not-trying-to-be)
- [Trade-offs](#trade-offs)
    - [JSON files instead of real external integrations](#json-files-instead-of-real-external-integrations)
    - [Synchronous command execution](#synchronous-command-execution)
    - [Fail-fast on unexpected sync errors](#fail-fast-on-unexpected-sync-errors)
    - [Separate DTO and repository layers](#separate-dto-and-repository-layers)
    - [Publish preview instead of full delivery](#publish-preview-instead-of-full-delivery)
- [How failures are treated](#how-failures-are-treated)
    - [Validation failures](#validation-failures)
    - [Unexpected sync failures](#unexpected-sync-failures)
- [Why Pimcore is a good fit here](#why-pimcore-is-a-good-fit-here)
- [Summary](#summary)

## Goal

This repository is a small reference bundle that shows a realistic Pimcore-oriented backend workflow for structured travel data.

The main goal is to demonstrate:

- feed import from JSON files
- DTO mapping
- validation
- normalization into Pimcore Data Objects
- a simple publish preview flow

It is not meant to be a full travel system.

It is also meant to reflect the kind of backend work I want this repository to represent:

- Pimcore-oriented backend development
- Symfony service and command design
- structured feed import and normalization
- pragmatic data modeling
- maintainable PHP code

---

## Main design choices

### 1. Use console commands as entry points

The bundle is built around explicit commands:

- `travel:sync:catalog`
- `travel:sync:availability`
- `travel:publish:preview`

I chose this because it keeps the workflows easy to understand and easy to run locally.

For a public reference project, commands are a good fit because they make the main behavior visible without requiring extra infrastructure.

### 2. Use file-based JSON feeds

The current input format is a local JSON file.

That is a simplification on purpose.

The point of this repository is to show what happens after data enters the system:

- reading
- mapping
- validation
- normalization
- persistence
- payload preparation

I did not want to turn this into a larger HTTP integration example with auth, retries, pagination, and transport concerns.

### 3. Keep catalog sync and availability sync separate

Catalog data and availability data are handled by different commands and different services.

This is intentional.

They represent different types of travel data and usually change at different speeds. Keeping them separate makes the code easier to follow and avoids forcing different concerns into one service.

### 4. Map external input into DTOs first

Raw supplier rows are not used directly in the sync logic.

They are first mapped into feed DTOs:

- `CatalogOfferDto`
- `TravelOfferAvailabilityDto`

This gives the rest of the code a more predictable structure and keeps raw input handling in one place.

### 5. Validate before persisting

Validation runs on the mapped feed DTOs before persistence continues.

This keeps invalid supplier input from leaking deeper into the sync flow.

The validation in this repository is intentionally simple. It is there to show the pattern, not to model a full production rule set.

### 6. Use narrower DTOs for Pimcore persistence

For catalog sync, valid feed DTOs are mapped further into narrower DTOs:

- `DestinationDto`
- `HotelDto`
- `TravelOfferDto`

I chose this because the supplier feed shape and the Pimcore persistence shape are not exactly the same.

This extra step makes the code more explicit and keeps persistence logic cleaner.

### 7. Let sync services do the orchestration

The main orchestration lives in:

- `CatalogSyncService`
- `AvailabilitySyncService`

These services coordinate:

- feed reading
- validation
- mapping
- repository calls
- relation assignment
- sync reporting

This keeps the commands thin and avoids pushing too much logic into repositories.

### 8. Keep Pimcore access behind repositories

All Pimcore lookup, create, and update logic is placed in repository classes.

That keeps Pimcore-specific code out of commands, validators, and mappers.

It also makes the code easier to navigate because the persistence boundary is obvious.

### 9. Keep relation assignment separate

Relations are linked through dedicated services:

- `TravelOfferAssigner`
- `TravelOfferAvailabilityAssigner`

I kept this separate from repository logic because saving an object and linking objects are related, but still different responsibilities.

This is a small thing, but it keeps the sync flow easier to read.

### 10. Support dry-run for sync commands

The sync context supports dry-run execution.

That makes it possible to demonstrate the import flow without persisting changes.

For a showcase repository, this is useful because it shows intent and flow without requiring every run to modify Pimcore data.

### 11. Use fail-fast handling for unexpected sync errors

Validation errors are treated as normal bad input and counted in the report.

Unexpected runtime or persistence problems are handled differently.

In catalog sync, the current behavior is intentionally fail-fast for unexpected exceptions. That is a conscious trade-off for this showcase.

The goal here is clarity, not maximum bulk import resilience.

### 12. Build a publish preview instead of a full export integration

The repository includes a publish preview flow that builds JSON from synchronized Pimcore data.

This is intentional.

I wanted to show that the synchronized data can be prepared for downstream use, without expanding the project into a full delivery/export system.

So this repository shows payload preparation, but not transport to external systems.

### 13. Keep Pimcore usage data-centric

Pimcore is used here as a structured data and content layer.

That fits the use case well because the project deals with:

- destinations
- hotels
- travel offers
- travel periods
- relations between them

I did not want to use Pimcore as a booking engine or as a frontend showcase. The point is to show a backend data workflow where Pimcore makes sense naturally.

---

## What this repository is not trying to be

This repository is not trying to be:

- a full travel platform
- a booking engine
- a full Pimcore application
- a generic ETL framework
- a multi-supplier reconciliation system
- a production-ready export platform

The scope is intentionally smaller.

The focus is on a believable backend bundle that shows data import, mapping, validation, persistence, and publish preparation in a Pimcore context.

---

## Trade-offs

### JSON files instead of real external integrations

This keeps the setup smaller and easier to run, but it does not show real transport-level integration work.

### Synchronous command execution

This keeps the flows readable and simple, but it does not show queue-based processing or background jobs.

### Fail-fast on unexpected sync errors

This makes failures visible immediately and keeps the example straightforward, but it does not try to continue processing after every technical error.

### Separate DTO and repository layers

This adds some structure and a bit more code, but it makes the responsibilities easier to understand.

### Publish preview instead of full delivery

This shows downstream shaping clearly, but stops before the final export or API handoff step.

---

## How failures are treated

There are two main kinds of failure in this repository.

### Validation failures

These come from bad or incomplete supplier input.

They are treated as expected input problems and reflected in the sync report.

### Unexpected sync failures

These are technical failures during processing or persistence.

The current implementation does not try to solve every recovery scenario. For this repository, it is more important that failures are clear than that the example becomes overloaded with retry and recovery logic.

---

## Why Pimcore is a good fit here

This repository is built around a Pimcore-shaped use case.

Pimcore works well here because the project needs:

- structured objects
- relations between objects
- a clear internal content/data model
- a publish step based on normalized records

That makes it a better fit than a generic demo with arbitrary entities.

The point is not just to store data somewhere. The point is to show a believable backend workflow where Pimcore is a natural part of the solution.

---

## Summary

The main choices in this repository are simple on purpose:

- command-driven workflows
- file-based feed input
- DTO mapping before persistence
- separate catalog and availability sync flows
- repository-based Pimcore persistence
- separate relation assigners
- dry-run support
- publish preview instead of full export delivery

These choices keep the bundle small, understandable, and honest as a public reference project.

The goal is not to cover everything.

The goal is to show a realistic backend structure that fits both the problem and the level of the project.
