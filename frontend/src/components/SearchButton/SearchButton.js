import './SearchButton.css'

const SearchButton = () => {

    const handleClick = () => {
        alert('Search Button')
    }

    return (
        <button className='search' onClick={handleClick}/>
    )
}

export default SearchButton