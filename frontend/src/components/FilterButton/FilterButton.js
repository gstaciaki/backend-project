import './FilterButton.css'

const FilterButton = () => {
    
    const handleClick = () => {
        alert('Filter Button')
    }

    return (
        <button className="filter" onClick={handleClick}/>
    )
}

export default FilterButton