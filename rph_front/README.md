# Front-end portion

For setup instructions read `README.md` that is located one level up.

This was my first React experience ever. The project structure IS messy.

- `App.js` is the entry point. Has two big components - Overview and Management.
- `Overview` component shows general list of rental properties, their dependencies on each other (dependency tree) and also single rental property relatives, if clicked.
- `Management` component allows to add new rental properties as well as assign any property a parent.
- Zustand package used for simple state management.
- The UI looks bad due to insufficient time. Had done very little styling.
- Error handling is missing, read console log to see if any errors should come up.
- Didn't know whether to use semicolons or not, so those are present in some places and in some - not.
