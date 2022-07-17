import Overview from './components/Overview';
import {useStore} from './store';
import {useEffect} from 'react';
import Management from './components/Management';

const App = () => {
  const rentalProperties = useStore((state) => state.rentalProperties);
  const fetchRentalProperties = useStore(state => state.fetchRentalProperties);
  const fetchParentChildRelations = useStore(state => state.fetchParentChildRelations);

  useEffect(() => {
    fetchRentalProperties();
  }, [fetchRentalProperties]);

  useEffect(() => {
    if (Object.keys(rentalProperties).length === 0) {
      return;
    }
    fetchParentChildRelations();
  }, [rentalProperties, fetchParentChildRelations]);


  return (
    <div className='main-container column-flex-container'>
        <Overview />
        <Management />
    </div>
  );
}

export default App;
