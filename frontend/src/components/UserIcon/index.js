import './UserIcon.css'

const UserIcon = (props) => {

    const handleClick = () => {
        alert('User icon')
    }

    const getInitials = (name) => {
        return name.slice(0, 1)
    }

    return (
        <button className='icon' onClick={handleClick}>{getInitials(props.text)}</button>
    )
}

export default UserIcon