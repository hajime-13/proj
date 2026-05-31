# TODO - Laravel Final Project

## Auth + Base Layout
- [x] Add Laravel Breeze package
- [x] Install Breeze (Blade stack)
- [ ] Configure dashboard route/controller redirect after login
- [ ] Replace/default views layout with Bootstrap-based UI (navbar/sidebar)

## Database
- [ ] Update `users` migration: add `profile_picture_path`
- [ ] Update `orders` migration: add `user_id`, `item_name`, `quantity`, `price`
- [ ] Update `Order` model fillable + relationships

## Dashboard
- [ ] Implement `DashboardController@index` (counts for users + orders)
- [ ] Create dashboard chart view using Chart.js

## Toast Notifications
- [ ] Add global toast partial (Bootstrap toast)
- [ ] Flash `session('success'|'error')` on register, users CRUD insert, orders CRUD insert

## Users CRUD
- [ ] Implement `UsersController` with index/create/store/edit/update/destroy
- [ ] Create views: users table + add/edit forms

## Orders CRUD (Second Module)
- [ ] Implement `OrdersController` with index/create/store/edit/update/destroy
- [ ] Scope listing to logged-in user (where user_id = auth()->id())
- [ ] Create views: orders table + add/edit forms

## Profile
- [ ] Update `ProfileController` to support avatar upload
- [ ] Update `ProfileUpdateRequest` to include avatar validation + optional fields
- [ ] Create/adjust profile view for avatar upload

## Migrations / Run
- [ ] Ensure `php artisan migrate` works
- [ ] Ensure `php artisan storage:link` works
- [ ] Manual test full flow: register -> toast -> login -> dashboard -> CRUD -> profile

