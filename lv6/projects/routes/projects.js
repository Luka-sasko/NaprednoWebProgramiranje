const express = require('express');
const router = express.Router();
const Project = require('../models/Project');

// Svi projekti
router.get('/', async (req, res) => {
  const projects = await Project.find();
  res.render('index', { projects });
});

// Nova forma
router.get('/new', (req, res) => {
  res.render('new');
});

// Kreiraj projekt
router.post('/', async (req, res) => {
  const data = req.body;
  data.clanovi = Array.isArray(data.clanovi) ? data.clanovi : [data.clanovi];
  await Project.create(data);
  res.redirect('/projects');
});

// Detalji
router.get('/:id', async (req, res) => {
  const project = await Project.findById(req.params.id);
  res.render('show', { project });
});

// Edit forma
router.get('/:id/edit', async (req, res) => {
  const project = await Project.findById(req.params.id);
  res.render('edit', { project });
});

// Ažuriraj
router.put('/:id', async (req, res) => {
  const data = req.body;
  data.clanovi = Array.isArray(data.clanovi) ? data.clanovi : [data.clanovi];
  await Project.findByIdAndUpdate(req.params.id, data);
  res.redirect('/projects/' + req.params.id);
});

// Obriši
router.delete('/:id', async (req, res) => {
  await Project.findByIdAndDelete(req.params.id);
  res.redirect('/projects');
});

module.exports = router;
