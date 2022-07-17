DROP TABLE IF EXISTS parent_child_relations;
DROP TABLE IF EXISTS rental_properties;

CREATE TABLE rental_properties (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `shareable` TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT PK_rental_property PRIMARY KEY (`id`),
    CONSTRAINT U_rental_property_title UNIQUE (`title`)
) ENGINE=InnoDB;

CREATE TABLE parent_child_relations (
    `id` INT NOT NULL AUTO_INCREMENT,
    `parent_id` INT NOT NULL,
    `child_id` INT NOT NULL,
    CONSTRAINT PK_parent_child_relations PRIMARY KEY (`id`),
    CONSTRAINT FK_parent_child_relations_parent_id_rental_properties_id FOREIGN KEY (`parent_id`) REFERENCES rental_properties(`id`) ON DELETE CASCADE,
    CONSTRAINT FK_parent_child_relations_child_id_rental_properties_id FOREIGN KEY (`child_id`) REFERENCES rental_properties(`id`) ON DELETE CASCADE,
    CONSTRAINT U_parent_child_relations_parent_id_child_id UNIQUE (`parent_id`, `child_id`)
) ENGINE=InnoDB;


-- Initial test values.
INSERT INTO rental_properties (`title`) VALUES
    ('Building complex'),
    ('Building 1'),
    ('Building 2'),
    ('Building 3'),
    ('Parking space 1'),
    ('Parking space 4'),
    ('Parking space 8'),
    ('My property');
INSERT INTO rental_properties (`title`, `shareable`) VALUES ('Shared parking space 1', 1);

INSERT INTO parent_child_relations (`parent_id`, `child_id`) VALUES
    (
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building complex'),
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building 1')
    ),
    (
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building complex'),
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building 2')
    ),
    (
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building complex'),
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building 3')
    ),
    (
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building 1'),
        (SELECT `id` FROM rental_properties WHERE `title` = 'Parking space 1')
    ),
    (
        (SELECT `id` FROM rental_properties WHERE `title` = 'Parking space 1'),
        (SELECT `id` FROM rental_properties WHERE `title` = 'My property')
    ),
    (
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building 2'),
        (SELECT `id` FROM rental_properties WHERE `title` = 'Parking space 4')
    ),
    (
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building 2'),
        (SELECT `id` FROM rental_properties WHERE `title` = 'Shared parking space 1')
    ),
    (
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building 3'),
        (SELECT `id` FROM rental_properties WHERE `title` = 'Shared parking space 1')
    ),
    (
        (SELECT `id` FROM rental_properties WHERE `title` = 'Building 3'),
        (SELECT `id` FROM rental_properties WHERE `title` = 'Parking space 8')
    );
