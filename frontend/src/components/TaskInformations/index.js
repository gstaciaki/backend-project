import DetailsBlock from '../DetailsBlock'
import TaskTitle from '../TaskTitle'
import './TaskInformations.css'

const TaskInformations = () => {
    return (
        <div className='informations'>
            <TaskTitle />
            <DetailsBlock />
        </div>
    )
}

export default TaskInformations 