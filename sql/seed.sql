-- ==================================
-- LIMPIAR (opcional para reset dev)
-- ==================================

delete from settings;
delete from weights;
delete from users;

-- ==================================
-- USERS BASE
-- ==================================

-- password = admin123
insert into users (name,email,password,role)
values
(
  'Maria',
  'admin@local',
  '$2y$10$Qk2ydzAh7YpI4lkgc1V7hOg7Q3JJ3Jq9oqr8S7vAnYQ9o3FfZ8P7K',
  'super_admin'
);

-- password = admin123
insert into users (name,email,password,role)
values
(
  'Admin Demo',
  'admin@test.com',
  '$2y$10$Qk2ydzAh7YpI4lkgc1V7hOg7Q3JJ3Jq9oqr8S7vAnYQ9o3FfZ8P7K',
  'admin'
);

-- password = user123
insert into users (name,email,password,role)
values
(
  'User Demo',
  'user@test.com',
  '$2y$10$LRC5tPb0xZ1M9kAC1zM2bOGmD.O9HspWg2l6kA8m1q7Z.rLhzR1lS',
  'user'
);

-- ==================================
-- SETTINGS DEMO
-- ==================================

insert into settings (user_id,target)
values
(1,70),
(2,75),
(3,68);
