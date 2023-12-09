import './DetailsBlock.css'
import { useSelectedTaskContext } from '../../context/SelectedTaskContext'
import UsersIcons from '../UsersIcons'

const DetailsBlock = () => {
    const { selectedTask } = useSelectedTaskContext()

    return (
        <div className='detailsBlock'>
            <table>
                <thead>Detalhes</thead>
                <tbody>
                    <th>
                        <td>Data de Criacao</td>
                        <td>Data de Finalizacao</td>
                        <td>Prioridade</td>
                        <td>Responsavel</td>
                    </th>
                    <tr>
                        <td>{selectedTask.createdAt}</td>
                        <td>{selectedTask.finishedAt}</td>
                        <td>{selectedTask.priority}</td>
                        <td>{<UsersIcons owners={selectedTask.owners} />}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    )
}

export default DetailsBlock 