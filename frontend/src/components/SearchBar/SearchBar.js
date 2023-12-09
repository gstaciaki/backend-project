import FilterButton from '../FilterButton/FilterButton'
import SearchButton from '../SearchButton/SearchButton'
import SearchInput from '../SearchInput/SearchInput'
import './SearchBar.css'

const SearchBar = () => {
    return (
        <div className='bar'>
            <SearchButton />
            <SearchInput />
            <FilterButton />
        </div>
    )
}

export default SearchBar