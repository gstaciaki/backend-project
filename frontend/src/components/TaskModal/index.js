import TaskButtons from '../TaskButtons'
import TaskContent from '../TaskContent'
import './TaskModal.css'

const TaskModal = ({ onCloseButtonClick }) => {
    return (
        <div className='ModalBody'>
            <section>
                <TaskButtons onCloseButtonClick={onCloseButtonClick} />
                <TaskContent />
            </section>
        </div>
    )
}

export default TaskModal 