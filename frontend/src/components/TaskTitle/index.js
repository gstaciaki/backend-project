import './TaskTitle.css'
import { useSelectedTaskContext } from '../../context/SelectedTaskContext'

const TaskTitle = () => {
    const { selectedTask } = useSelectedTaskContext()
    return (
        <div className='taskTitle'>
            <label>{selectedTask.title}</label>
        </div>
    )
}

export default TaskTitle