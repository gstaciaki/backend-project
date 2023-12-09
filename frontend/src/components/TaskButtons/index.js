import CloseButton from '../CloseButton'
import OptionsButton from '../OptionsButton'
import './TaskButtons.css'

const TaskButtons = ({ onCloseButtonClick }) => {
    return (
        <div className='buttons'>
            <CloseButton onClick={onCloseButtonClick} />
            <OptionsButton />
        </div>

    )
}

export default TaskButtons