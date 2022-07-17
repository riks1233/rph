// Overview page

import React from 'react'
import {useStore} from '../store'
import RentalPropertyListItem from './RentalPropertyListItem';

const Overview = () => {
  const rentalProperties = useStore((state) => state.rentalProperties);
  const relationsTree = useStore((state) => state.relationsTree);

  return (
    <div>
      <h1 className='centered-text'>Overview</h1>
      <div className='row-flex-container align-start'>
        <div className='logical-block'>
          <h2>Rental properties list</h2>
          <ul>
            {Object.entries(rentalProperties).map(([id, data]) =>
              <RentalPropertyListItem key={id} id={id} content={data.title}/>
            )}

          </ul>
        </div>

        <div className='horizontal-separator--big'></div>
        <div className='vertical-line'></div>


        <div className='logical-block'>
          <h2>Relations tree</h2>
          <ul>
            {relationsTree}
          </ul>
        </div>

        <div className='horizontal-separator--big'></div>

        <Relatives />

      </div>
    </div>
  )
}

const Relatives = () => {
  const relatives = useStore((state) => state.relatives);
  const rentalProperties = useStore((state) => state.rentalProperties);
  const selectedIdForRelatives = useStore((state) => state.selectedIdForRelatives);

  const RelativesTitle = () => {

    let title = '';
    if (Object.keys(rentalProperties).length > 0 && selectedIdForRelatives > -1) {
      title = rentalProperties[selectedIdForRelatives].title;
    }
    return(
      <h3 className='RelativesTitle bold-text'>{title}</h3>
    );
  }

  return (
    <div className='logical-block'>
      <h2>Rental property relatives</h2>
      <RelativesTitle />
      <ul>
          {
          // Potentially a bad approach to set component keys, but in this case we are
          // dealing with display data, which is not interactable, so seems fine.
          relatives.map((relative, i) => {
            if (relative.relation !== null) {
              return (<li key={i}>
                {relative.title} ({relative.relation})
              </li>);
            }
            return;
          })}
      </ul>
    </div>
  );
}

export default Overview
