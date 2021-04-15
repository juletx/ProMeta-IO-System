CREATE DATABASE IF NOT EXISTS project;

CREATE OR REPLACE TABLE projects (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    name varchar(50),
    description varchar(1000),
    process varchar(50) NOT NULL,
    current_iteration int(11) NOT NULL,
    start_date date,
    end_date date,
    version varchar(50),
    FOREIGN KEY (process) REFERENCES methodology.processes (guid)
);

CREATE OR REPLACE TABLE members (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    name varchar(1000)
);

CREATE OR REPLACE TABLE project_members (
    project int(11) NOT NULL,
    member int(11) NOT NULL,
    role varchar(50) NOT NULL,
    FOREIGN KEY (project) REFERENCES projects (id),
    FOREIGN KEY (member) REFERENCES members (id),
    FOREIGN KEY (role) REFERENCES methodology.roles (guid)
);

CREATE OR REPLACE TABLE iterations (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    project int(11),
    iteration varchar(50),
    number int(11),
    start_date date,
    end_date date,
    current_activity int(11) NOT NULL,
    FOREIGN KEY (project) REFERENCES projects (id),
    FOREIGN KEY (iteration) REFERENCES methodology.iterations (guid)
);

CREATE OR REPLACE TABLE activities (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    iteration int(11) NOT NULL,
    current_task int(11) NOT NULL,
    FOREIGN KEY (iteration) REFERENCES iterations (id)
);

CREATE OR REPLACE TABLE tasks (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    task varchar(50) NOT NULL,
    member int(11) NOT NULL,
    FOREIGN KEY (task) REFERENCES methodology.tasks (guid),
    FOREIGN KEY (member) REFERENCES members (id)
);

CREATE OR REPLACE TABLE artifacts (
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    artifact varchar(50) NOT NULL,
    filename varchar(1000),
    FOREIGN KEY (artifact) REFERENCES methodology.artifacts (guid)
);

CREATE OR REPLACE TABLE task_artifacts (
    task int(11) NOT NULL,
    artifact int(11) NOT NULL,
    FOREIGN KEY (task) REFERENCES tasks (id),
    FOREIGN KEY (artifact) REFERENCES artifacts (id)
);

ALTER TABLE projects 
ADD FOREIGN KEY (current_iteration) REFERENCES iterations (id);

ALTER TABLE iterations
ADD FOREIGN KEY (current_activity) REFERENCES activities (id);

ALTER TABLE activities
ADD FOREIGN KEY (current_task) REFERENCES tasks (id);