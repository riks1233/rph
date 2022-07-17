import React from 'react'
import { useStore } from '../store';

const RentalPropertyListItem = ({id, content}) => {
  const getRelativesOf = useStore((state) => state.getRelativesOf);
  return (
    <li className='RentalPropertyListItem' onClick={(e) => getRelativesOf(id)}>{content}</li>
  )
}

export default RentalPropertyListItem
