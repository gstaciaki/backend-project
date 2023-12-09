import './TaskContent.css'
import TaskInformations from '../TaskInformations'
import Comments from '../Comments'

const TaskContent = () => {
    return (
        <div className='content'>
            <TaskInformations />
            <Comments />
        </div>
    )
}

export default TaskContent 