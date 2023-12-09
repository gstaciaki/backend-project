import './SearchInput.css'

const SearchInput = () => {
    
    const handleKeyPress = (event) => {
        if(event.key === 'Enter') {
            alert('Press Enter')
        }
    }
    
    return (
        <input className="searchInput" placeholder='Pesquisar...' onKeyDown={handleKeyPress} />
    )
}

export default SearchInput