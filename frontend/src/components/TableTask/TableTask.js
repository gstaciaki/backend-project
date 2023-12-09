import React, { useEffect, useState } from 'react';
import { TaskProvider, useTaskContext } from '../../context/TaskContext';
import { SelectedTaskProvider, useSelectedTaskContext } from '../../context/SelectedTaskContext';
import NewTaskButton from '../NewTaskButton';
import TableBody from '../TableBody';
import TableTitle from '../TableTitle/TableTitle';
import './TableTask.css';
import NewTaskField from '../NewTaskField';
import TaskModal from '../TaskModal';
import fetchTasks from '../../services/taskService';

const TableTask = () => {
  const { tasks, setTasks } = useTaskContext();
  const { selectedTask, setSelectedTask } = useSelectedTaskContext();
  const [isNewTaskFieldPresent, setIsNewTaskFieldPresent] = useState(false);

  useEffect(() => {
    const fetchAndSetTasks = async () => {
      const tasksData = await fetchTasks();
      setTasks(tasksData);
    };

    fetchAndSetTasks();
  }, [setTasks]);

  const handleEnter = (event) => {
    if (event.key !== 'Enter') return;
    setTasks([...tasks, { title: event.target.value, createdAt: '', finishedAt: '', owner: '', priority: '' }]);
    setIsNewTaskFieldPresent(false);
  };

  const addNewTask = () => {
    if (!isNewTaskFieldPresent) {
      setIsNewTaskFieldPresent(true);
      const newTask = { field: <NewTaskField onKeyDown={handleEnter} />, isNewTask: true };
      setSelectedTask(newTask);
    }
  };

  const handleTaskClick = (task) => {
    if (!task.isNewTask) {
      setSelectedTask(task);
    }
  };

  const closeModal = () => {
    setIsNewTaskFieldPresent(false);
    setSelectedTask(null);
  };

  return (
    <div className='tableHead'>
      <table>
        <TableTitle />
        <TableBody rows={tasks} onClick={handleTaskClick} />
        {isNewTaskFieldPresent && (
          <tr className='newTaskField'>
            <td colSpan="5">{selectedTask.field}</td>
          </tr>
        )}
        <NewTaskButton onClick={addNewTask} />
      </table>

      {selectedTask && !selectedTask.isNewTask && !isNewTaskFieldPresent && (
        <TaskModal task={selectedTask} onCloseButtonClick={closeModal} />
      )}

    </div>
  );
};

const WrappedTableTask = () => {
  return (
    <TaskProvider>
      <SelectedTaskProvider>
        <TableTask />
      </SelectedTaskProvider>
    </TaskProvider>
  );
};

export default WrappedTableTask;