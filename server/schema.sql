BEGIN TRANSACTION;

CREATE SCHEMA querybook;

CREATE TABLE querybook.queries (
  id uuid NOT NULL,
  query text NOT NULL,
  PRIMARY KEY (id)
);

COMMIT TRANSACTION;
