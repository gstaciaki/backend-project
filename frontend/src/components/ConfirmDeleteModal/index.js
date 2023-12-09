import React, { useState } from 'react';
import './ConfirmDeleteModal.css';

const ConfirmDeleteModal = () => {
  const [isVisible, setIsVisible] = useState(true);

  const handleDelete = () => {
    // Lógica para excluir a tarefa
    // ...

    // Fechar o modal após excluir
    setIsVisible(false);
    window.location.reload();
  };

  const handleCancel = () => {
    setIsVisible(false);

  };

  return (
    isVisible && (
      <div className='confirmDelete'>
        <section id='back'>
          <p>Deseja Excluir Esta Tarefa?</p>
          <div className='buttons'>
            <button onClick={handleDelete}>Excluir</button>
            <button onClick={handleCancel}>Cancelar</button>
          </div>
        </section>
      </div>
    )
  );
};

export default ConfirmDeleteModal;
