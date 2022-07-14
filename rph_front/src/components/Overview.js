// Overview page

import React from 'react'

const Overview = () => {

  // let rentalPropertiesJson = `[{"id":1,"title":"Building complex","shareable":0},{"id":2,"title":"Building 1","shareable":0},{"id":3,"title":"Building 2","shareable":0},{"id":4,"title":"Building 3","shareable":0},{"id":5,"title":"Parking space 1","shareable":0},{"id":6,"title":"Parking space 4","shareable":0},{"id":7,"title":"Parking space 8","shareable":0},{"id":8,"title":"Shared parking space 1","shareable":0},{"id":9,"title":"bomzhiha","shareable":1},{"id":11,"title":"banzai","shareable":0}]`
  let rentalPropertiesJson = `{"1":{"title":"Building complex","shareable":0},"2":{"title":"Building 1","shareable":0},"3":{"title":"Building 2","shareable":0},"4":{"title":"Building 3","shareable":0},"5":{"title":"Parking space 1","shareable":0},"6":{"title":"Parking space 4","shareable":0},"7":{"title":"Parking space 8","shareable":0},"8":{"title":"Shared parking space 1","shareable":0},"9":{"title":"bomzhiha","shareable":1},"11":{"title":"banzai","shareable":0}}`

  let rentalPropertiesList = JSON.parse(rentalPropertiesJson)
  // console.log(rentalPropertiesList[1])

  // for (const [id, properties] of Object.entries(rentalPropertiesList)) {
  //   console.log(`${id}: [${properties.title}]`)
  // }


  // for (const [id, properties] of Object.entries(rentalPropertiesList)) {
  //   console.log(`${id}: [${properties.title}]`)
  // }

  // console.log(Object.entries(rentalPropertiesList))


  let treeJson = ``;

  return (
    <div>
      <h1>Overview</h1>
      <div>
        <h2>Rental properties list</h2>
        <ul>
          {/* {rentalPropertiesList.map((rentalProperty) =>
            <li>{rentalProperty.title}</li>
          )} */}
          {Object.entries(rentalPropertiesList).map(([id, properties]) =>
            // console.log(properties.title)
            <RentalPropertiesListItem title={properties.title}/>
          )}

        </ul>
      </div>

      <div>
        <h2>Rental properties relations tree</h2>

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
