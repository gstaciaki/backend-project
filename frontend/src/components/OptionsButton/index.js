import React, { useState } from 'react';
import './OptionsButton.css';
import EditButton from '../EditButton';
import DeleteButton from '../DeleteButton';

const OptionsButton = () => {
    const [mostrarComponentes, setMostrarComponentes] = useState(false);

    const toggleComponentes = () => {
        setMostrarComponentes(!mostrarComponentes);
    };

    return (
        <div className='options'>
            {mostrarComponentes && (
                <div>
                    <EditButton />
                    <DeleteButton />
                </div>
            )}
            <button onClick={toggleComponentes}></button>
        </div>
    );
};

export default OptionsButton;
