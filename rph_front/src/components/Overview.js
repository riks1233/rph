// Overview page

import React from 'react'
import {useState} from 'react'
import {useStore} from '../store'

const Overview = () => {

  const [title, setTitle] = useState("");
  const [shareable, setShareable] = useState(false);

  const [rentalPropertyId, setRentalPropertyId] = useState(-1);
  const [parentId, setParentId] = useState(-1);


  const rentalPropertyCreateFormSubmit = (e) => {
    e.preventDefault();
    console.log(`Form submitted with values: ${title}, ${shareable}`);
  }

  const parentAssignFormSubmit = (e) => {
    e.preventDefault();
    console.log(rentalPropertyId);
    console.log(`Form submitted with values: {property_id: ${rentalPropertyId}, parent_id: ${parentId}`);
  }

  let variableFromStore = useStore((state => state.variable))
  // console.log(variableFromStore);

  // let rentalPropertiesJson = `[{"id":1,"title":"Building complex","shareable":0},{"id":2,"title":"Building 1","shareable":0},{"id":3,"title":"Building 2","shareable":0},{"id":4,"title":"Building 3","shareable":0},{"id":5,"title":"Parking space 1","shareable":0},{"id":6,"title":"Parking space 4","shareable":0},{"id":7,"title":"Parking space 8","shareable":0},{"id":8,"title":"Shared parking space 1","shareable":0},{"id":9,"title":"bomzhiha","shareable":1},{"id":11,"title":"banzai","shareable":0}]`
  let rentalPropertiesJson = `{"1":{"title":"Building complex","shareable":0},"2":{"title":"Building 1","shareable":0},"3":{"title":"Building 2","shareable":0},"4":{"title":"Building 3","shareable":0},"5":{"title":"Parking space 1","shareable":0},"6":{"title":"Parking space 4","shareable":0},"7":{"title":"Parking space 8","shareable":0},"8":{"title":"Shared parking space 1","shareable":0},"9":{"title":"bomzhiha","shareable":1},"11":{"title":"banzai","shareable":0}}`

  let rentalPropertiesList = JSON.parse(rentalPropertiesJson)


  // for (const [id, properties] of Object.entries(rentalPropertiesList)) {
  //   console.log(`${id}: [${properties.title}]`)
  // }


  // for (const [id, properties] of Object.entries(rentalPropertiesList)) {
  //   console.log(`${id}: [${properties.title}]`)
  // }

  // console.log(Object.entries(rentalPropertiesList))


  let treeJson = ``;
  let relativesJson = `[{"title":"Building 1","relation":"parent"},{"title":"Building 2","relation":"parent"},{"title":"Parking space 4","relation":"sibling"},{"title":"Parking space 8","relation":"sibling"},{"title":"Shared parking space 1","relation":null}]`;
  let relativesList = JSON.parse(relativesJson);

  return (
    <div>
      <h1>Overview</h1>
      <div>
        <h2>Rental properties list</h2>
        <ul>
          {/* {rentalPropertiesList.map((rentalProperty) =>
            <li>{rentalProperty.title}</li>
          )} */}
          {Object.entries(rentalPropertiesList).map(([id, data]) =>
            // console.log(properties.title)
            <RentalPropertiesListItem key={id} title={data.title}/>
          )}

        </ul>
      </div>

      <div>
        <h2>Rental properties relations tree</h2>

      </div>

      <div>
        <h2>Rental property relatives</h2>
        <h3>Rental property title</h3>
        <ul>
            {
            // Potentially a bad approach to set component keys, but in this case we are
            // dealing with display data, which is not interactable, so seems fine.
            relativesList.map((relative, i) =>
              <li key={i}>{relative.title} ({relative.relation})</li>
            )}
        </ul>

      </div>

      <div>
        <h2>Create rental property</h2>
        <form onSubmit={rentalPropertyCreateFormSubmit}>
          <div>
            <label>Rental property title</label>
            <input
              type="text"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
            />
          </div>
          <div>
            <label>Shareable between other rental properties?</label>
            <input
              type="checkbox"
              checked={shareable}
              onChange={(e) => setShareable(e.currentTarget.checked)}
            />
          </div>
          <input type="submit" value="Create" />
        </form>
      </div>

      <div>
        <h2>Assign parent to a rental property</h2>
        <form onSubmit={parentAssignFormSubmit}>
          <div>
            <label>Choose rental property</label>
            <select value={rentalPropertiesList[rentalPropertyId]} onChange={setRentalPropertyId}>
              <option key={-1} value={-1}></option>
              {Object.entries(rentalPropertiesList).map(([id, data]) =>
                <option key={id} value={id}>{data.title}</option>
              )}
            </select>

          </div>
          <div>
            <label>Choose parent</label>
            <select value={rentalPropertiesList[parentId]} onChange={setParentId}>
              <option key={-1} value={-1}></option>
              {Object.entries(rentalPropertiesList).map(([id, data]) =>
                <option key={id} value={id}>{data.title}</option>
              )}
            </select>
          </div>
          <input type="submit" value="Assign" />
        </form>

      </div>

    </div>
  )
}


const RentalPropertiesListItem = ({title}) => {
  return (
    <li>{title}</li>
  )
}


// export const RentalPropertiesList = ({}) => {
//   return (
//     <ul>

//     </ul>
//   )
// }



export default Overview
