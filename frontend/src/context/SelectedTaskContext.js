import React, { createContext, useContext, useState } from 'react';

const SelectedTaskContext = createContext();

export const SelectedTaskProvider = ({ children }) => {
  const [selectedTask, setSelectedTask] = useState(null);

  return (
    <SelectedTaskContext.Provider value={{ selectedTask, setSelectedTask }}>
      {children}
    </SelectedTaskContext.Provider>
  );
};

export const useSelectedTaskContext = () => {
  return useContext(SelectedTaskContext);
};