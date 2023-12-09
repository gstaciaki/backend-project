import React, { useState } from 'react';
import './DeleteButton.css';
import ConfirmDeleteModal from '../ConfirmDeleteModal';

const DeleteButton = () => {
  const [showModal, setShowModal] = useState(false);

  const openModal = () => {
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
  };

  return (
    <div onClick={openModal} className='delete'>
      <button></button>
      <label>Excluir Tarefa</label>

      {showModal && <ConfirmDeleteModal onClose={closeModal} />}
    </div>
  );
};

export default DeleteButton;
