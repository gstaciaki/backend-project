import UserIcon from '../UserIcon'
import './UsersIcons.css'

const UsersIcons = (props) => {
    if(!props.owners) return 
    const users = props.owners.map(u => {
        return (
            <UserIcon text={u} />
        )
    })

    return (
        <div className="usersBlock">
            {users.reverse()}
        </div>
    )
}

export default UsersIcons