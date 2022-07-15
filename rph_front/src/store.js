import create from 'zustand'

let store = (set) => ({
  variable: 'Variable from store',
})

export const useStore = create(store)
