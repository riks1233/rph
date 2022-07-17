import React from 'react'
import { useStore } from '../store';
import { useState } from 'react';
import { customPost } from '../general';

const Management = () => {
  return (
    <div>
      <div className='vertical-separator--big'></div>
      <h1 className='centered-text'>Management</h1>
      <div className='row-flex-container align-start'>
        <CreateForm />
        <div className='horizontal-separator--big'></div>
        <ParentAssignForm />
      </div>
    </div>
  )
}

const CreateForm = () => {
  const [title, setTitle] = useState("");
  const [shareable, setShareable] = useState(false);
  const fetchRentalProperties = useStore(state => state.fetchRentalProperties);

  const createFormSubmit = async (e) => {
    e.preventDefault();
    let rentalPropertyValues = {
      title: title,
      shareable: (shareable ? 1 : 0),
    };
    const responseBody = await customPost('create_new', rentalPropertyValues);
    if (responseBody.success === 1) {
      fetchRentalProperties();
      setTitle('');
      setShareable(false);
      return;
    }

    console.log(`Fetch error: ${responseBody.error_msg}`)
  }

  return (
    <div className='logical-block'>
      <h2>Create rental property</h2>
      <form onSubmit={createFormSubmit}>
        <div className='column-flex-container'>
          <label>Rental property title</label>
          <input
            type="text"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
          />
        </div>
        <div className='vertical-separator--small'></div>
        <div className='column-flex-container'>
          <label>Shareable between other rental properties?</label>
          <label className='checkbox-container'>
            <input
              type="checkbox"
              checked={shareable}
              onChange={(e) => setShareable(e.currentTarget.checked)}
            />
            <span className="checkmark"></span>
          </label>
        </div>
        <input type="submit" value="Create" />
      </form>
    </div>
  )
}

const ParentAssignForm = () => {
  const [rentalPropertyId, setRentalPropertyId] = useState(-1);
  const [parentId, setParentId] = useState(-1);
  const rentalProperties = useStore((state) => state.rentalProperties);
  const fetchParentChildRelations = useStore(state => state.fetchParentChildRelations);

  const parentAssignFormSubmit = async (e) => {
    e.preventDefault();
    let relation = {
      rental_property_id: rentalPropertyId,
      parent_id: parentId,
    }
    const responseBody = await customPost('assign_parent', relation);
    if (responseBody.success === 1) {
      fetchParentChildRelations();
      return;
    }

    console.log(`Fetch error: ${responseBody.error_msg}`)
  }

  return (
    <div className="ParentAssignForm">
      <h2>Assign parent to a rental property</h2>
      <form onSubmit={parentAssignFormSubmit}>
        <div className="column-flex-container">
          <label>Choose parent</label>
          <select onChange={(e) => setParentId(e.target.value)}>
            <option key={-1} value={-1}></option>
            {Object.entries(rentalProperties).map(([id, data]) =>
              <option key={id} value={id}>{data.title}</option>
            )}
          </select>
        </div>
        <div className='vertical-separator--small'></div>
        <div className='column-flex-container'>
          <label>Choose rental property</label>
          <select onChange={(e) => setRentalPropertyId(e.target.value)}>
            <option key={-1} value={-1}></option>
            {Object.entries(rentalProperties).map(([id, data]) =>
              <option key={id} value={id}>{data.title}</option>
            )}
          </select>
        </div>
        <input type="submit" value="Assign" />
      </form>
    </div>
  )
}

export default Management
