const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');
const methodOverride = require('method-override');

const app = express();
mongoose.connect('mongodb://localhost:27017/lv6_projects', {
  useNewUrlParser: true,
  useUnifiedTopology: true,
});

const projectsRoute = require('./routes/projects');

app.set('view engine', 'jade');
app.use(express.static('public'));
app.use(bodyParser.urlencoded({ extended: true }));
app.use(methodOverride('_method'));

app.use('/projects', projectsRoute);

app.get('/', (req, res) => {
  res.redirect('/projects');
});

app.listen(3000, () => console.log('Server running on http://localhost:3000'));
