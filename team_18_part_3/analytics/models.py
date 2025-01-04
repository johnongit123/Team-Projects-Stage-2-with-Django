from django.db import models
from chat.models import Employee

# Create your models here.

class Projects(models.Model):
  project_id = models.AutoField(primary_key = True, db_column='ProjectID')
  project_name = models.TextField(db_column='ProjectName')
  project_desc = models.TextField(db_column='Description')
  project_deadline = models.DateField(db_column='DueDate')
  project_status = models.TextField(db_column='Status')
  manager_id = models.ForeignKey(Employee, on_delete = models.CASCADE, db_column='ManagerID')
  project_colour = models.TextField(db_column='Colour')

  class Meta:
          db_table = 'projects'

class Tasks(models.Model):
  task_id = models.AutoField(primary_key = True, db_column='TaskID')
  task_name = models.TextField(db_column='TaskName')
  task_desc = models.TextField(db_column='TaskDescription')
  task_status = models.TextField(db_column='TaskStatus')
  task_deadline = models.DateField(db_column='DueDate')
  task_private = models.BooleanField(db_column='IsPrivate')
  task_duration = models.IntegerField(db_column='TaskDuration')
  task_colour = models.TextField(db_column='Colour')

  class Meta:
          db_table = 'tasks'

class Project_Task(models.Model):
  task_id = models.ForeignKey(Tasks, primary_key=True, on_delete = models.CASCADE, db_column='TaskID')
  project_id = models.ForeignKey(Projects, on_delete=models.CASCADE, db_column='ProjectID')

  class Meta:
          db_table = 'taskprojects'

class Project_Employee(models.Model):
  project_id = models.ForeignKey(Projects, on_delete = models.CASCADE, db_column='ProjectID')
  user_id = models.ForeignKey(Employee, on_delete=models.CASCADE, db_column='UserID')

  class Meta:
          db_table = 'project-employee table'

class User_Tasks(models.Model):

    user_id = models.ForeignKey(Employee, on_delete=models.CASCADE, db_column='UserID')
    task_id = models.ForeignKey(Tasks, on_delete = models.CASCADE, db_column='TaskID')
    
    class Meta:
        db_table = 'usertasks'


class Project_Graph(models.Model):
    ENUM_CHOICES1 = [
        ('OPTION1', 'bar'),
        ('OPTION2', 'pie'),
        ('OPTION3', 'column'),
    ]

    project_id = models.ForeignKey(Projects, on_delete=models.CASCADE, db_column='ProjectID')
    graph_id = models.AutoField(primary_key=True, db_column='GraphID')
    graph_title = models.CharField(max_length=255, db_column='graph_title')
    graph_content = models.TextField(db_column='graph_content')
    graph_type = models.CharField(max_length=25, choices=ENUM_CHOICES1, db_column='graph_type')
    graph_filter_complete = models.BooleanField(db_column='graph_filter_complete')
    graph_filter_not_started = models.BooleanField(db_column='graph_filter_not_started')
    graph_filter_ongoing = models.BooleanField(db_column='graph_filter_ongoing')
    graph_filter_overdue = models.BooleanField(db_column='graph_filter_overdue')
    graph_filter_paused = models.BooleanField(db_column='graph_filter_paused')

    class Meta:
        db_table = 'project_graph_table'
