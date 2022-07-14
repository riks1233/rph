# Requirements

- Display full tree.
- Display rental property list independently.
- Display specific property relatives.
- Add new rental property (possibility to mark as shareable)
  - New properties that are not shareable are displayed as root properties until assigned a parent.
- Move a property under any parent in the tree. Shareable properties are inserted once under a single parent.
  - Non-shareable properties, that already do have parent, can be assigned to a new parent (they are just moved under the new parent then.)

# Realization

2 tabs:
- Overview (3 columns)
  - Simple list of properties.
  - Full dependency tree.
  - Possibility to select property (from the tree) and display its relatives.
- Management (2 columns)
  - Add new property.
  - Move property under a parent.
