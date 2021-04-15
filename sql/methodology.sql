CREATE DATABASE IF NOT EXISTS methodology;

CREATE OR REPLACE TABLE methodologies (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    version varchar(1000)
);

CREATE OR REPLACE TABLE processes (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    methodology varchar(50) NOT NULL,
    FOREIGN KEY (methodology) REFERENCES methodologies (guid)
);

CREATE OR REPLACE TABLE role_sets (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    methodology varchar(50) NOT NULL,
    FOREIGN KEY (methodology) REFERENCES methodologies (guid)
);

CREATE OR REPLACE TABLE roles (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    role_set varchar(50) NOT NULL,
    FOREIGN KEY (role_set) REFERENCES role_sets (guid)
);

CREATE OR REPLACE TABLE domains (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    methodology varchar(50) NOT NULL,
    FOREIGN KEY (methodology) REFERENCES methodologies (guid)
);

CREATE OR REPLACE TABLE disciplines (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    methodology varchar(50) NOT NULL,
    FOREIGN KEY (methodology) REFERENCES methodologies (guid)
);

CREATE OR REPLACE TABLE practices (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    methodology varchar(50) NOT NULL,
    FOREIGN KEY (methodology) REFERENCES methodologies (guid)
);

CREATE OR REPLACE TABLE phases (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    process varchar(50) NOT NULL,
    FOREIGN KEY (process) REFERENCES processes (guid)
);

CREATE OR REPLACE TABLE iterations (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    phase varchar(50) NOT NULL,
    FOREIGN KEY (phase) REFERENCES phases (guid)
);

CREATE OR REPLACE TABLE activities (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000)
);

CREATE OR REPLACE TABLE phase_activities (
    phase varchar(50) NOT NULL,
    activity varchar(50) NOT NULL,
    PRIMARY KEY (phase, activity),
    FOREIGN KEY (phase) REFERENCES phases (guid),
    FOREIGN KEY (activity) REFERENCES activities (guid)
);

CREATE OR REPLACE TABLE iteration_activities (
    iteration varchar(50) NOT NULL,
    activity varchar(50) NOT NULL,
    PRIMARY KEY (iteration, activity),
    FOREIGN KEY (iteration) REFERENCES iterations (guid),
    FOREIGN KEY (activity) REFERENCES activities (guid)
);

CREATE OR REPLACE TABLE tasks (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    discipline varchar(50),
    FOREIGN KEY (discipline) REFERENCES disciplines (guid)
);

CREATE OR REPLACE TABLE role_tasks (
    role varchar(50) NOT NULL,
    task varchar(50) NOT NULL,
    PRIMARY KEY (role, task),
    FOREIGN KEY (role) REFERENCES roles (guid),
    FOREIGN KEY (task) REFERENCES tasks (guid)
);

CREATE OR REPLACE TABLE phase_tasks (
    phase varchar(50) NOT NULL,
    task varchar(50) NOT NULL,
    PRIMARY KEY (phase, task),
    FOREIGN KEY (phase) REFERENCES phases (guid),
    FOREIGN KEY (task) REFERENCES tasks (guid)
);

CREATE OR REPLACE TABLE iteration_tasks (
    iteration varchar(50) NOT NULL,
    task varchar(50) NOT NULL,
    PRIMARY KEY (iteration, task),
    FOREIGN KEY (iteration) REFERENCES iterations (guid),
    FOREIGN KEY (task) REFERENCES tasks (guid)
);

CREATE OR REPLACE TABLE activity_tasks (
    activity varchar(50) NOT NULL,
    task varchar(50) NOT NULL,
    PRIMARY KEY (activity, task),
    FOREIGN KEY (activity) REFERENCES activities (guid),
    FOREIGN KEY (task) REFERENCES tasks (guid)
);

CREATE OR REPLACE TABLE task_sections (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    section_description varchar(1000),
    task varchar(50) NOT NULL,
    FOREIGN KEY (task) REFERENCES tasks (guid)
);

CREATE OR REPLACE TABLE templates (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    attachment varchar(1000)
);

CREATE OR REPLACE TABLE artifacts (
    guid varchar(50) PRIMARY KEY,
    name varchar(1000),
    presentation_name varchar(1000),
    brief_description varchar(1000),
    domain varchar(50),
    template varchar(50),
    FOREIGN KEY (domain) REFERENCES domains (guid),
    FOREIGN KEY (template) REFERENCES templates (guid)
);

CREATE OR REPLACE TABLE input_artifacts (
    task varchar(50) NOT NULL,
    artifact varchar(50) NOT NULL,
    PRIMARY KEY (task, artifact),
    FOREIGN KEY (task) REFERENCES tasks (guid),
    FOREIGN KEY (artifact) REFERENCES artifacts (guid)
);

CREATE OR REPLACE TABLE output_artifacts (
    task varchar(50) NOT NULL,
    artifact varchar(50) NOT NULL,
    PRIMARY KEY (task, artifact),
    FOREIGN KEY (task) REFERENCES tasks (guid),
    FOREIGN KEY (artifact) REFERENCES artifacts (guid)
);

CREATE OR REPLACE TABLE role_artifacts (
    role varchar(50) NOT NULL,
    artifact varchar(50) NOT NULL,
    PRIMARY KEY (role, artifact),
    FOREIGN KEY (role) REFERENCES roles (guid),
    FOREIGN KEY (artifact) REFERENCES artifacts (guid)
);