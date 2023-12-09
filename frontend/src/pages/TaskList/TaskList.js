import './TaskList.css'
import GraySquare from "../../components/GraySquare/GraySquare"
import SearchBar from '../../components/SearchBar/SearchBar'
import WrappedTableTask from '../../components/TableTask/TableTask'

const TaskList = () => {
    return (
        <div className='main'>
            <SearchBar/>
            <GraySquare>
                <WrappedTableTask/>
            </GraySquare>
        </div>
    )
}

export default TaskList