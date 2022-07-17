import create from 'zustand'
import { v4 as uuidv4 } from 'uuid';
import { customGet } from './general';
import RentalPropertyListItem from './components/RentalPropertyListItem';


const generateRelationsTreeRecursive = (jsxArray, parentChildRelations, rentalProperties, rentalPropertyId, level = 0) => {
  const indentiation = '--'.repeat(level);
  // jsxArray.push(<li key={uuidv4()}>{indentiation} {rentalProperties[rentalPropertyId].title}</li>);
  jsxArray.push(<RentalPropertyListItem key={uuidv4()} id={rentalPropertyId}
    content={`${indentiation} ${rentalProperties[rentalPropertyId].title}`}
  />);
  if (parentChildRelations.hasOwnProperty(rentalPropertyId)) {
    let childrenIds = parentChildRelations[rentalPropertyId]
    childrenIds.forEach(childId => {
      generateRelationsTreeRecursive(jsxArray, parentChildRelations, rentalProperties, childId, level + 1)
    });
  }
}

let store = (set) => ({
  rentalProperties: {},
  fetchRentalProperties: async () => {
    const fetchedRentalProperties = await customGet('get_all', {});
    set((state) => ({rentalProperties: fetchedRentalProperties}));
  },


  relationsTree: [],
  fetchParentChildRelations: async () => {
    const parentChildRelationsAndRootIds = await customGet('get_tree', {});
    if (Object.keys(parentChildRelationsAndRootIds).length === 0) {
      return;
    }

    let rootIds = parentChildRelationsAndRootIds.root_ids;
    let parentChildRelations = parentChildRelationsAndRootIds.parent_child_relations;

    set((state) => {
      let jsxArray = [];
      rootIds.forEach(rootId => {
        generateRelationsTreeRecursive(jsxArray, parentChildRelations, state.rentalProperties, rootId);
      });
      return ({relationsTree: jsxArray});
    });
  },


  selectedIdForRelatives: -1,
  relatives: [],
  getRelativesOf: async (id) => {
    const fetchedRelatives = await customGet(`get_relatives_of?rental_property_id=${id}`, []);
    set((state) => ({relatives: fetchedRelatives, selectedIdForRelatives: id}));
  },

})

export const useStore = create(store)
