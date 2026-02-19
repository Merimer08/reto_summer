-- ==================================
-- USERS
-- ==================================

create table if not exists users (
    id serial primary key,
    name varchar(120) not null,
    email varchar(150) unique not null,
    password varchar(255) not null,
    role varchar(20) not null default 'user',
    created_at timestamp default now()
);

-- ==================================
-- WEIGHTS
-- ==================================

create table if not exists weights (
    id serial primary key,
    user_id integer not null references users(id) on delete cascade,
    date date not null,
    weight numeric(5,2) not null,
    created_at timestamp default now()
);

create index if not exists idx_weights_user
on weights(user_id);

create index if not exists idx_weights_date
on weights(date);

-- ==================================
-- SETTINGS (objetivo)
-- ==================================

create table if not exists settings (
    id serial primary key,
    user_id integer unique not null references users(id) on delete cascade,
    target numeric(5,2),
    created_at timestamp default now()
);
